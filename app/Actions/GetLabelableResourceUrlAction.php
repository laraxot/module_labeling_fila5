<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * URL della pagina di dettaglio Filament di un modello: ogni modulo ha il proprio pannello
 * (`{modulo}::admin`), quindi si cercano tutti i pannelli. Preferisce `view`, poi `edit`.
 */
class GetLabelableResourceUrlAction
{
    use QueueableAction;

    public function execute(Model $model): ?string
    {
        foreach (Filament::getPanels() as $panel) {
            $resource = $panel->getModelResource($model);
            if ($resource === null) {
                continue;
            }
            Assert::subclassOf($resource, Resource::class);

            foreach (['view', 'edit'] as $page) {
                if ($resource::hasPage($page)) {
                    return $resource::getUrl($page, ['record' => $model], panel: $panel->getId());
                }
            }
        }

        return null;
    }
}
