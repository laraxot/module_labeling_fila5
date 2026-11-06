<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Labeling\Http\Controllers\ResolveLabelCodeController;

/*
 * Il QR stampato codifica `{APP_URL}/l/{code}`: aperto con la fotocamera del telefono porta alla
 * pagina di dettaglio Filament del modello identificato (l'autenticazione la chiede il pannello).
 * La route e' pubblica: `throttle` (config labeling.resolve_rate_limit, richieste al minuto per IP)
 * limita l'enumerazione dei codici.
 */
Route::get('/l/{code}', ResolveLabelCodeController::class)
    ->where('code', '.+')
    ->middleware('throttle:'.max(1, config()->integer('labeling.resolve_rate_limit', 30)).',1')
    ->name('labeling.resolve');
