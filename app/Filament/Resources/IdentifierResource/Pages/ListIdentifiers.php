<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Resources\IdentifierResource\Pages;

use Filament\Actions\Action;
use Modules\Labeling\Filament\Resources\IdentifierResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseListRecords;

class ListIdentifiers extends XotBaseListRecords
{
    protected static string $resource = IdentifierResource::class;

    /**
     * Nessun "crea": l'assegnazione passa da AssignIdentifierAction.
     *
     * @return array<string, Action>
     */
    #[\Override]
    protected function getHeaderActions(): array
    {
        return [];
    }
}
