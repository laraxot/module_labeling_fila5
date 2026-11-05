<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelPrintResource\Pages;

use Filament\Actions\Action;
use Modules\Labeling\Filament\Resources\LabelPrintResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListLabelPrints extends XotBaseListRecords
{
    protected static string $resource = LabelPrintResource::class;

    /**
     * Le stampe nascono da PrintLabelAction: niente "crea".
     *
     * @return array<string, Action>
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [];
    }
}
