<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelPrintResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Modules\Labeling\Filament\Resources\LabelPrintResource;
use Modules\Labeling\Filament\Resources\LabelPrintResource\Tables\LabelPrintsTable;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;

/**
 * Lang delle azioni: Modules/Labeling/lang/{locale}/label_print.php (`actions.download`, `actions.reprint`).
 */
class ViewLabelPrint extends XotBaseViewRecord
{
    protected static string $resource = LabelPrintResource::class;

    /**
     * @return array<string, Action|ActionGroup>
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            'download' => LabelPrintsTable::downloadAction()->button(),
            'reprint' => LabelPrintsTable::reprintAction()->button(),
        ];
    }
}
