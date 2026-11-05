<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelTemplateResource\Tables;

use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Xot\Filament\Resources\Tables\XotBaseResourceTable;

/**
 * Lang: Modules/Labeling/lang/{locale}/label_templates.php. Azioni di riga: default Xot (view/edit/delete).
 */
class LabelTemplatesTable extends XotBaseResourceTable
{
    /**
     * @return array<string, Column>
     */
    public function getTableColumns(): array
    {
        return [
            'code' => TextColumn::make('code')->searchable()->sortable(),
            'name' => TextColumn::make('name')->searchable()->sortable(),
            'format' => TextColumn::make('format')->badge()->sortable(),
            'labelable_type' => TextColumn::make('labelable_type')
                ->formatStateUsing(static fn (?string $state): string => $state === null || $state === '' ? '—' : class_basename($state))
                ->sortable(),
            'width_mm' => TextColumn::make('width_mm')->numeric()->sortable(),
            'height_mm' => TextColumn::make('height_mm')->numeric()->sortable(),
            'dpi' => TextColumn::make('dpi')->numeric()->toggleable(isToggledHiddenByDefault: true),
            'is_default' => IconColumn::make('is_default')->boolean(),
            'is_active' => IconColumn::make('is_active')->boolean(),
            'updated_at' => TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    /**
     * @return array<string, BaseFilter>
     */
    public function getTableFilters(): array
    {
        return [
            'format' => SelectFilter::make('format')->options(LabelFormatEnum::class),
            'is_active' => TernaryFilter::make('is_active'),
        ];
    }
}
