<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\PrinterResource\Tables;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Modules\Labeling\Enums\PrinterDriverEnum;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

/**
 * Lang: Modules/Labeling/lang/{locale}/printers.php.
 */
class PrintersTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable(),
            'driver' => TextColumn::make('driver')->badge()->sortable(),
            'host' => TextColumn::make('host')->searchable(),
            'port' => TextColumn::make('port'),
            'dpi' => TextColumn::make('dpi')->numeric()->toggleable(isToggledHiddenByDefault: true),
            'is_active' => IconColumn::make('is_active')->boolean(),
        ];
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getTableFilters(): array
    {
        return [
            'driver' => SelectFilter::make('driver')->options(PrinterDriverEnum::class),
            'is_active' => TernaryFilter::make('is_active'),
        ];
    }
}
