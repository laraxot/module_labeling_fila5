<?php

declare(strict_types=1);

namespace Modules\Labeling\Models\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Models\Identifier;
use Modules\Labeling\Models\LabelPrint;

/**
 * Per i modelli che implementano {@see \Modules\Labeling\Contracts\LabelableContract}.
 *
 * Le relazioni funzionano anche se il modello sta su un'altra connessione: Identifier e
 * LabelPrint dichiarano la propria (`labeling`).
 *
 * @property-read Collection<int, Identifier> $identifiers
 * @property-read Collection<int, LabelPrint> $labelPrints
 *
 * @phpstan-require-extends Model
 */
trait HasIdentifiers
{
    /**
     * @return MorphMany<Identifier, $this>
     */
    public function identifiers(): MorphMany
    {
        return $this->morphMany(Identifier::class, 'identifiable');
    }

    /**
     * @return MorphMany<LabelPrint, $this>
     */
    public function labelPrints(): MorphMany
    {
        return $this->morphMany(LabelPrint::class, 'labelable');
    }

    public function activeIdentifier(IdentifierTypeEnum $type): ?Identifier
    {
        return $this->identifiers()
            ->where('type', $type->value)
            ->where('is_active', true)
            ->latest('assigned_at')
            ->first();
    }
}
