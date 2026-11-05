<?php

declare(strict_types=1);

namespace Modules\Labeling\Contracts;

/**
 * Modello che si puo' etichettare (StockUnit, Location, ...). Va implementato da un Eloquent Model
 * che usa il trait {@see \Modules\Labeling\Models\Traits\HasIdentifiers}.
 */
interface LabelableContract
{
    /** Codice leggibile stampato e codificato nel QR (univoco per tipo di modello). */
    public function getLabelCode(): string;

    /** @return array<string, string|int|float|null> segnaposti per i template */
    public function getLabelData(): array;
}
