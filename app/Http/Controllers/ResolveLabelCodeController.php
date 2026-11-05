<?php

declare(strict_types=1);

namespace Modules\Labeling\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\Labeling\Actions\GetLabelableResourceUrlAction;
use Modules\Labeling\Actions\ResolveIdentifiableAction;

/**
 * `GET /l/{code}` (route `labeling.resolve`): redirect alla pagina di dettaglio Filament del modello
 * identificato dal codice, 404 se il codice non e' attivo o il modello non ha una resource.
 */
class ResolveLabelCodeController
{
    public function __invoke(string $code): RedirectResponse
    {
        $model = app(ResolveIdentifiableAction::class)->execute($code);
        abort_if($model === null, 404);

        $url = app(GetLabelableResourceUrlAction::class)->execute($model);
        abort_if($url === null, 404);

        return redirect()->to($url);
    }
}
