<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Actions\Concerns;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Modules\Labeling\Actions\PrintLabelAction;
use Modules\Labeling\Actions\ResolveLabelTemplateAction;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Exceptions\LabelingException;
use Modules\Labeling\Models\LabelTemplate;
use Modules\Labeling\Models\Printer;
use Modules\Xot\Actions\Cast\SafeIntCastAction;
use Modules\Xot\Actions\GetTransKeyAction;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webmozart\Assert\Assert;

/**
 * Configurazione comune di PrintLabelTableAction e PrintLabelHeaderAction.
 *
 * Etichetta, tooltip, titoli e notifiche vengono dal file lang dell'azione
 * (`labeling::print_label_table` / `labeling::print_label_header`, chiave `actions.print_label.*`)
 * e non da quello della Resource che la usa: l'azione e' riusata da altri moduli (Warehouse, Roosko)
 * e deve presentarsi uguale ovunque, come le azioni PDF di Xot. I campi del modale prendono
 * l'etichetta automatica (`fields.*`) dallo stesso file.
 *
 * Il bottone chiama soltanto {@see PrintLabelAction}; se la stampa produce un PDF lo restituisce
 * come download.
 */
trait ConfiguresPrintLabelAction
{
    public static function getDefaultName(): ?string
    {
        return 'print_label';
    }

    protected function configurePrintLabel(): void
    {
        $this
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->label(fn (): string => $this->transPrintLabel('label'))
            ->tooltip(fn (): string => $this->transPrintLabel('tooltip'))
            ->modalHeading(fn (): string => $this->transPrintLabel('modal_heading'))
            ->modalDescription(fn (): string => $this->transPrintLabel('modal_description'))
            ->modalSubmitActionLabel(fn (): string => $this->transPrintLabel('submit'))
            ->visible(static fn (?Model $record): bool => $record instanceof LabelableContract)
            ->fillForm(fn (?Model $record): array => [
                'label_template_id' => $this->defaultTemplateId($record),
                'printer_id' => null,
                'copies' => 1,
            ])
            ->schema(fn (?Model $record): array => [
                'label_template_id' => Select::make('label_template_id')
                    ->options(fn (): array => $this->templateOptions($record))
                    ->searchable()
                    ->required(),
                'printer_id' => Select::make('printer_id')
                    ->options(static fn (): array => Printer::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all()),
                'copies' => TextInput::make('copies')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(config()->integer('labeling.max_copies', 100))
                    ->required(),
            ])
            ->action(
                /** @param array<string, mixed> $data */
                fn (array $data, ?Model $record): ?StreamedResponse => $this->printLabel($data, $record),
            );
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    protected function printLabel(array $data, ?Model $record): ?StreamedResponse
    {
        if (! $record instanceof LabelableContract) {
            return null;
        }

        $templateId = SafeIntCastAction::cast($data['label_template_id'] ?? null);
        $printerId = SafeIntCastAction::cast($data['printer_id'] ?? null);

        try {
            $print = app(PrintLabelAction::class)->execute(
                $record,
                $templateId > 0 ? LabelTemplate::query()->find($templateId) : null,
                $printerId > 0 ? Printer::query()->find($printerId) : null,
                SafeIntCastAction::cast($data['copies'] ?? 1, 1),
            );
        } catch (LabelingException|\InvalidArgumentException $e) {
            Notification::make()
                ->title($this->transPrintLabel('error'))
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }

        Notification::make()
            ->title($this->transPrintLabel('success'))
            ->body($print->status->getLabel())
            ->success()
            ->send();

        if ($print->output_path === null) {
            return null;
        }

        $disk = Storage::disk(config()->string('labeling.pdf.disk', 'local'));
        Assert::isInstanceOf($disk, FilesystemAdapter::class);

        return $disk->download($print->output_path);
    }

    /**
     * @return array<int, string>
     */
    protected function templateOptions(?Model $record): array
    {
        $types = $record instanceof Model ? [$record->getMorphClass(), $record::class] : [];

        /** @var array<int, string> $options */
        $options = LabelTemplate::query()
            ->where('is_active', true)
            ->where(static fn (Builder $query): Builder => $query
                ->whereNull('labelable_type')
                ->orWhere('labelable_type', '')
                ->orWhereIn('labelable_type', $types))
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        return $options;
    }

    protected function defaultTemplateId(?Model $record): ?int
    {
        if (! $record instanceof LabelableContract) {
            return null;
        }

        try {
            return app(ResolveLabelTemplateAction::class)->execute($record)->id;
        } catch (LabelingException) {
            return null;
        }
    }

    protected function transPrintLabel(string $key): string
    {
        $fullKey = app(GetTransKeyAction::class)->execute(static::class).'.actions.print_label.'.$key;
        $translated = __($fullKey);

        return is_string($translated) && $translated !== $fullKey ? $translated : $key;
    }
}
