<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources;

use Filament\Resources\RelationManagers\RelationManager;
use Modules\Labeling\Filament\Resources\IdentifierResource\Pages\ListIdentifiers;
use Modules\Labeling\Filament\Resources\IdentifierResource\Pages\ViewIdentifier;
use Modules\Labeling\Models\Identifier;
use Modules\Xot\Filament\Resources\XotBaseResource;

/**
 * Consultazione e revoca: gli identificatori si assegnano con AssignIdentifierAction dalle
 * resource dei modelli Labelable, quindi qui non ci sono create/edit.
 */
class IdentifierResource extends XotBaseResource
{
    protected static ?string $model = Identifier::class;

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
            'index' => ListIdentifiers::route('/'),
            'view' => ViewIdentifier::route('/{record}'),
        ];
    }
}
