<?php

declare(strict_types=1);

namespace Modules\Labeling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Labeling\Database\Factories\PrinterFactory;
use Modules\Labeling\Enums\PrinterDriverEnum;

/**
 * Stampante di etichette (ZPL via TCP raw, oppure "PDF da scaricare").
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property PrinterDriverEnum $driver
 * @property string|null $host
 * @property int $port
 * @property int $dpi
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection<int, LabelPrint> $labelPrints
 *
 * @method static Builder<static>|Printer newModelQuery()
 * @method static Builder<static>|Printer newQuery()
 * @method static Builder<static>|Printer query()
 * @method static PrinterFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class Printer extends BaseModel
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'driver',
        'host',
        'port',
        'dpi',
        'is_active',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'port' => 9100,
        'dpi' => 203,
        'is_active' => true,
    ];

    /**
     * @return HasMany<LabelPrint, $this>
     */
    public function labelPrints(): HasMany
    {
        return $this->hasMany(LabelPrint::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'driver' => PrinterDriverEnum::class,
            'port' => 'integer',
            'dpi' => 'integer',
            'is_active' => 'boolean',
        ]);
    }
}
