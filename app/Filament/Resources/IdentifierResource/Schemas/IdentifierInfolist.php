<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\IdentifierResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class IdentifierInfolist extends XotBaseResourceInfolist
{
    /**
     * @return array<string, Component>
     */
    public function getInfolistSchema(): array
    {
        return [
            'type' => TextEntry::make('type')->badge(),
            'value' => TextEntry::make('value')->copyable()->fontFamily('mono'),
            'identifiable_type' => TextEntry::make('identifiable_type')
                ->formatStateUsing(static fn (string $state): string => class_basename($state)),
            'identifiable_id' => TextEntry::make('identifiable_id'),
            'is_active' => IconEntry::make('is_active')->boolean(),
            'assigned_at' => TextEntry::make('assigned_at')->dateTime(),
            'revoked_at' => TextEntry::make('revoked_at')->dateTime(),
        ];
    }
}
