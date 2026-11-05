<?php

declare(strict_types=1);

namespace Modules\Labeling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Labeling\Database\Factories\LabelTemplateFactory;
use Modules\Labeling\Enums\LabelFormatEnum;

/**
 * Template di etichetta. `content` e' Blade: ZPL testuale o HTML (PDF). Variabili disponibili:
 * `$code`, `$url`, `$data`, `$qrSvg` (solo PDF), `$widthMm`, `$heightMm`, `$dpi`,
 * `$widthDots`, `$heightDots`.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property LabelFormatEnum $format
 * @property int $width_mm
 * @property int $height_mm
 * @property int $dpi
 * @property string $content
 * @property string|null $labelable_type
 * @property bool $is_default
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Collection<int, LabelPrint> $labelPrints
 *
 * @method static Builder<static>|LabelTemplate newModelQuery()
 * @method static Builder<static>|LabelTemplate newQuery()
 * @method static Builder<static>|LabelTemplate query()
 * @method static LabelTemplateFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class LabelTemplate extends BaseModel
{
    use SoftDeletes;

    /** @var list<string> */
    protected $fillable = [
        'code',
        'name',
        'format',
        'width_mm',
        'height_mm',
        'dpi',
        'content',
        'labelable_type',
        'is_default',
        'is_active',
    ];

    /** @var array<string, mixed> */
    protected $attributes = [
        'dpi' => 203,
        'is_default' => false,
        'is_active' => true,
    ];

    /**
     * @return HasMany<LabelPrint, $this>
     */
    public function labelPrints(): HasMany
    {
        return $this->hasMany(LabelPrint::class);
    }

    /** Millimetri convertiti in punti stampante alla risoluzione del template. */
    public function mmToDots(int $mm): int
    {
        return (int) round($mm / 25.4 * $this->dpi);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'format' => LabelFormatEnum::class,
            'width_mm' => 'integer',
            'height_mm' => 'integer',
            'dpi' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);
    }
}
