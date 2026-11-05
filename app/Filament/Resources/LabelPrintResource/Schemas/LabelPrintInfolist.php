<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\LabelPrintResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class LabelPrintInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'labelable_type' => TextEntry::make('labelable_type')
                ->formatStateUsing(static fn (string $state): string => class_basename($state)),
            'labelable_id' => TextEntry::make('labelable_id'),
            'label_template_id' => TextEntry::make('labelTemplate.code'),
            'printer_id' => TextEntry::make('printer.code'),
            'copies' => TextEntry::make('copies')->numeric(),
            'status' => TextEntry::make('status')->badge(),
            'printed_at' => TextEntry::make('printed_at')->dateTime(),
            'output_path' => TextEntry::make('output_path'),
            'error' => TextEntry::make('error')->columnSpanFull(),
            'rendered' => TextEntry::make('rendered')->fontFamily('mono')->columnSpanFull(),
        ];
    }
}
