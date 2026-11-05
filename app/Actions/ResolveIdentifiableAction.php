<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Models\Identifier;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dal valore scansionato al modello identificato, cercando tra gli identificatori attivi.
 *
 * La lettura si normalizza ({@see NormalizeScannedCodeAction}); i candidati sono il codice cosi' com'e'
 * per QR/Code128 e la forma esadecimale maiuscola per RFID/NFC. Restituisce null se nessun
 * identificatore attivo corrisponde o se il modello collegato non esiste piu'.
 */
class ResolveIdentifiableAction
{
    use QueueableAction;

    public function execute(string $scanned): ?Model
    {
        $scan = app(NormalizeScannedCodeAction::class)->execute($scanned);
        if ($scan->isEmpty()) {
            return null;
        }

        $candidates = [];
        foreach (IdentifierTypeEnum::cases() as $type) {
            if ($type->isPrinted()) {
                $candidates[] = Identifier::activeValueFor($type, $scan->code);

                continue;
            }

            $candidates[] = Identifier::activeValueFor($type, $scan->hex ?? strtoupper($scan->code));
        }

        $identifier = Identifier::query()
            ->whereIn('active_value', array_values(array_unique($candidates)))
            ->where('is_active', true)
            ->orderByDesc('assigned_at')
            ->first();

        $model = $identifier?->identifiable;

        return $model instanceof Model ? $model : null;
    }
}
