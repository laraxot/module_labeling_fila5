<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\PrinterResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

/**
 * Lang: Modules/Labeling/lang/{locale}/printer_infolist.php.
 */
class PrinterInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'code' => TextEntry::make('code')->copyable(),
            'name' => TextEntry::make('name'),
            'driver' => TextEntry::make('driver')->badge(),
            'host' => TextEntry::make('host'),
            'port' => TextEntry::make('port'),
            'dpi' => TextEntry::make('dpi')->numeric(),
            'is_active' => IconEntry::make('is_active')->boolean(),
            'updated_at' => TextEntry::make('updated_at')->dateTime(),
        ];
    }
}
