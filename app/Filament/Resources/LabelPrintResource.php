<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources;

use Filament\Resources\RelationManagers\RelationManager;
use Modules\Labeling\Filament\Resources\LabelPrintResource\Pages\ListLabelPrints;
use Modules\Labeling\Filament\Resources\LabelPrintResource\Pages\ViewLabelPrint;
use Modules\Labeling\Models\LabelPrint;
use Modules\Xot\Filament\Resources\XotBaseResource;

/**
 * Storico stampe in sola lettura, con download del PDF e ristampa.
 */
class LabelPrintResource extends XotBaseResource
{
    protected static ?string $model = LabelPrint::class;

    /**
     * @return array<string, class-string<RelationManager>>
     */
    #[\Override]
    public static function getRelations(): array
    {
        return [];
    }

    #[\Override]
    public static function getPages(): array
    {
        return [
            'index' => ListLabelPrints::route('/'),
            'view' => ViewLabelPrint::route('/{record}'),
        ];
    }
}
