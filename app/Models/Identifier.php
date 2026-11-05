<?php

declare(strict_types=1);

namespace Modules\Labeling\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Modules\Labeling\Database\Factories\IdentifierFactory;
use Modules\Labeling\Enums\IdentifierTypeEnum;

/**
 * Identificatore fisico (QR, Code128, RFID UHF, NFC) assegnato a un modello Labelable.
 *
 * `active_value` vale `"{type}:{value}"` finche' l'identificatore e' attivo e `null` dopo la
 * revoca: l'indice unique su quella colonna garantisce a livello DB che lo stesso valore non sia
 * attivo due volte per lo stesso tipo (piu' NULL sono ammessi da tutti i driver).
 *
 * @property int $id
 * @property string $identifiable_type
 * @property int $identifiable_id
 * @property IdentifierTypeEnum $type
 * @property string $value
 * @property string|null $active_value
 * @property bool $is_active
 * @property CarbonImmutable $assigned_at
 * @property CarbonImmutable|null $revoked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read Model|null $identifiable
 *
 * @method static Builder<static>|Identifier newModelQuery()
 * @method static Builder<static>|Identifier newQuery()
 * @method static Builder<static>|Identifier query()
 * @method static IdentifierFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class Identifier extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'identifiable_type',
        'identifiable_id',
        'type',
        'value',
        'active_value',
        'is_active',
        'assigned_at',
        'revoked_at',
    ];

    public static function activeValueFor(IdentifierTypeEnum $type, string $value): string
    {
        return $type->value.':'.$value;
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function identifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'identifiable_id' => 'integer',
            'type' => IdentifierTypeEnum::class,
            'is_active' => 'boolean',
            'assigned_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
        ]);
    }
}
