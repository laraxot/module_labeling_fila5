<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelPrintResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Modules\Labeling\Actions\PrintLabelAction;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Exceptions\LabelingException;
use Modules\Labeling\Models\LabelPrint;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webmozart\Assert\Assert;

/**
 * Lang: Modules/Labeling/lang/{locale}/label_prints.php.
 */
class LabelPrintsTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'id' => TextColumn::make('id')->sortable(),
            'labelable_type' => TextColumn::make('labelable_type')
                ->formatStateUsing(static fn (string $state): string => class_basename($state))
                ->sortable(),
            'labelable_id' => TextColumn::make('labelable_id')->sortable(),
            'label_template' => TextColumn::make('labelTemplate.code')->searchable(),
            'printer' => TextColumn::make('printer.code'),
            'copies' => TextColumn::make('copies')->numeric(),
            'status' => TextColumn::make('status')->badge()->sortable(),
            'printed_at' => TextColumn::make('printed_at')->dateTime()->sortable(),
            'error' => TextColumn::make('error')->limit(60)->toggleable(isToggledHiddenByDefault: true),
            'created_at' => TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getTableFilters(): array
    {
        return [
            'status' => SelectFilter::make('status')->options(LabelPrintStatusEnum::class),
            'label_template_id' => SelectFilter::make('label_template_id')->relationship('labelTemplate', 'name'),
            'printer_id' => SelectFilter::make('printer_id')->relationship('printer', 'name'),
        ];
    }

    /**
     * @return array<string, Action|ActionGroup>
     */
    public function getTableActions(): array
    {
        return [
            'view' => ViewAction::make()->iconButton(),
            'download' => self::downloadAction()->iconButton(),
            'reprint' => self::reprintAction()->iconButton(),
        ];
    }

    /**
     * @return array<string, BulkAction>
     */
    public function getTableBulkActions(): array
    {
        return [];
    }

    /** Download del PDF prodotto dal driver pdf_download. */
    public static function downloadAction(): Action
    {
        return Action::make('download')
            ->icon('heroicon-o-arrow-down-tray')
            ->visible(static fn (LabelPrint $record): bool => $record->output_path !== null)
            ->action(static function (LabelPrint $record): ?StreamedResponse {
                if ($record->output_path === null) {
                    return null;
                }
                $disk = Storage::disk(config()->string('labeling.pdf.disk', 'local'));
                Assert::isInstanceOf($disk, FilesystemAdapter::class);

                return $disk->download($record->output_path);
            });
    }

    /** Nuova stampa con stesso modello, template, stampante e copie. */
    public static function reprintAction(): Action
    {
        return Action::make('reprint')
            ->icon('heroicon-o-printer')
            ->requiresConfirmation()
            ->visible(static fn (LabelPrint $record): bool => $record->labelable instanceof LabelableContract)
            ->action(static function (LabelPrint $record): void {
                $labelable = $record->labelable;
                if (! $labelable instanceof LabelableContract) {
                    return;
                }

                try {
                    $print = app(PrintLabelAction::class)->execute($labelable, $record->labelTemplate, $record->printer, $record->copies);
                } catch (LabelingException|\InvalidArgumentException $e) {
                    Notification::make()->title($e->getMessage())->danger()->send();

                    return;
                }

                Notification::make()->title($print->status->getLabel())->success()->send();
            });
    }
}
