<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Labeling\Http\Controllers\ResolveLabelCodeController;

/*
 * Il QR stampato codifica url('/l/{code}'): aperto con la fotocamera del telefono porta alla
 * pagina di dettaglio Filament del modello identificato (l'autenticazione la chiede il pannello).
 */
Route::get('/l/{code}', ResolveLabelCodeController::class)
    ->where('code', '.+')
    ->name('labeling.resolve');
