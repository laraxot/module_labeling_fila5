<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Exceptions\IdentifierAlreadyAssignedException;
use Modules\Labeling\Models\Identifier;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Assegna un identificatore a un modello Labelable.
 *
 * - per `qr` / `barcode128` con `$value` null usa `getLabelCode()`; RFID e NFC richiedono il valore letto;
 * - il valore si normalizza come le letture ({@see NormalizeScannedCodeAction}): RFID/NFC in esadecimale maiuscolo;
 * - disattiva l'identificatore attivo dello stesso tipo del modello;
 * - il valore e' univoco tra gli attivi dello stesso tipo: lo garantisce l'indice unique su `active_value`,
 *   la verifica preventiva sotto lock serve solo a dare un messaggio chiaro;
 * - riassegnare lo stesso valore allo stesso modello restituisce l'identificatore esistente.
 *
 * @throws IdentifierAlreadyAssignedException
 */
class AssignIdentifierAction
{
    use QueueableAction;

    public function execute(Model&LabelableContract $model, IdentifierTypeEnum $type, ?string $value = null): Identifier
    {
        Assert::true($model->exists, (string) __('labeling::exceptions.labelable_not_saved'));

        $value = $this->normalize($type, $value ?? ($type->isPrinted() ? $model->getLabelCode() : ''));
        Assert::stringNotEmpty($value, (string) __('labeling::exceptions.identifier_value_required', ['type' => $type->getLabel()]));

        $morphClass = $model->getMorphClass();
        $modelKey = SafeIntCastAction::cast($model->getKey());
        $activeValue = Identifier::activeValueFor($type, $value);

        try {
            return (new Identifier)->getConnection()->transaction(function () use ($model, $type, $value, $morphClass, $modelKey, $activeValue): Identifier {
                $existing = Identifier::query()
                    ->where('active_value', $activeValue)
                    ->lockForUpdate()
                    ->first();

                if ($existing !== null) {
                    if ($existing->identifiable_type === $morphClass && $existing->identifiable_id === $modelKey) {
                        return $existing;
                    }

                    throw IdentifierAlreadyAssignedException::for($type, $value);
                }

                $current = Identifier::query()
                    ->where('identifiable_type', $morphClass)
                    ->where('identifiable_id', $modelKey)
                    ->where('type', $type->value)
                    ->where('is_active', true)
                    ->lockForUpdate()
                    ->get();

                foreach ($current as $identifier) {
                    app(RevokeIdentifierAction::class)->execute($identifier);
                }

                $identifier = new Identifier([
                    'type' => $type,
                    'value' => $value,
                    'active_value' => $activeValue,
                    'is_active' => true,
                    'assigned_at' => now()->toImmutable(),
                ]);
                $identifier->identifiable()->associate($model);
                $identifier->save();

                return $identifier;
            });
        } catch (UniqueConstraintViolationException $e) {
            throw IdentifierAlreadyAssignedException::for($type, $value, $e);
        }
    }

    private function normalize(IdentifierTypeEnum $type, string $value): string
    {
        $scan = app(NormalizeScannedCodeAction::class)->execute($value);

        if ($type->isHexadecimal()) {
            return $scan->hex ?? strtoupper($scan->code);
        }

        return $scan->code;
    }
}
