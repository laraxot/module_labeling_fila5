<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\IdentifierResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Modules\Labeling\Actions\RevokeIdentifierAction;
use Modules\Labeling\Filament\Resources\IdentifierResource;
use Modules\Labeling\Models\Identifier;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;

/**
 * Lang delle azioni: Modules/Labeling/lang/{locale}/identifier.php (`actions.revoke`).
 */
class ViewIdentifier extends XotBaseViewRecord
{
    protected static string $resource = IdentifierResource::class;

    /**
     * @return array<string, Action|ActionGroup>
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            'revoke' => Action::make('revoke')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(static fn (Identifier $record): bool => $record->is_active)
                ->action(static fn (Identifier $record): Identifier => app(RevokeIdentifierAction::class)->execute($record)),
        ];
    }
}
