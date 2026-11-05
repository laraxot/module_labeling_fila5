<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelTemplateResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

/**
 * Lang: Modules/Labeling/lang/{locale}/label_template_infolist.php.
 */
class LabelTemplateInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'code' => TextEntry::make('code')->copyable(),
            'name' => TextEntry::make('name'),
            'format' => TextEntry::make('format')->badge(),
            'labelable_type' => TextEntry::make('labelable_type')
                ->formatStateUsing(static fn (?string $state): string => $state === null || $state === '' ? '—' : class_basename($state)),
            'width_mm' => TextEntry::make('width_mm')->numeric(),
            'height_mm' => TextEntry::make('height_mm')->numeric(),
            'dpi' => TextEntry::make('dpi')->numeric(),
            'is_default' => IconEntry::make('is_default')->boolean(),
            'is_active' => IconEntry::make('is_active')->boolean(),
            'content' => TextEntry::make('content')
                ->fontFamily('mono')
                ->columnSpanFull(),
            'updated_at' => TextEntry::make('updated_at')->dateTime(),
        ];
    }
}
