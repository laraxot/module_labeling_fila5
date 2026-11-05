<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources;

use Filament\Resources\RelationManagers\RelationManager;
use Modules\Labeling\Filament\Resources\PrinterResource\Pages\CreatePrinter;
use Modules\Labeling\Filament\Resources\PrinterResource\Pages\EditPrinter;
use Modules\Labeling\Filament\Resources\PrinterResource\Pages\ListPrinters;
use Modules\Labeling\Filament\Resources\PrinterResource\Pages\ViewPrinter;
use Modules\Labeling\Models\Printer;
use Modules\Xot\Filament\Resources\XotBaseResource;

class PrinterResource extends XotBaseResource
{
    protected static ?string $model = Printer::class;

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
            'index' => ListPrinters::route('/'),
            'create' => CreatePrinter::route('/create'),
            'view' => ViewPrinter::route('/{record}'),
            'edit' => EditPrinter::route('/{record}/edit'),
        ];
    }
}
