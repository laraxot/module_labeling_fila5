<?php

declare(strict_types=1);

namespace Modules\Labeling\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Modules\Labeling\Database\Factories\LabelPrintFactory;
use Modules\Labeling\Enums\LabelPrintStatusEnum;

/**
 * Storico di una richiesta di stampa con lo snapshot del contenuto renderizzato.
 *
 * @property int $id
 * @property string $labelable_type
 * @property int $labelable_id
 * @property int $label_template_id
 * @property int|null $printer_id
 * @property int $copies
 * @property string $rendered
 * @property LabelPrintStatusEnum $status
 * @property string|null $error
 * @property string|null $output_path
 * @property CarbonImmutable|null $printed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property-read Model|null $labelable
 * @property-read LabelTemplate $labelTemplate
 * @property-read Printer|null $printer
 *
 * @method static Builder<static>|LabelPrint newModelQuery()
 * @method static Builder<static>|LabelPrint newQuery()
 * @method static Builder<static>|LabelPrint query()
 * @method static LabelPrintFactory factory($count = null, $state = [])
 *
 * @mixin \Eloquent
 */
class LabelPrint extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'labelable_type',
        'labelable_id',
        'label_template_id',
        'printer_id',
        'copies',
        'rendered',
        'status',
        'error',
        'output_path',
        'printed_at',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function labelable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<LabelTemplate, $this>
     */
    public function labelTemplate(): BelongsTo
    {
        return $this->belongsTo(LabelTemplate::class)->withTrashed();
    }

    /**
     * @return BelongsTo<Printer, $this>
     */
    public function printer(): BelongsTo
    {
        return $this->belongsTo(Printer::class)->withTrashed();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'labelable_id' => 'integer',
            'label_template_id' => 'integer',
            'printer_id' => 'integer',
            'copies' => 'integer',
            'status' => LabelPrintStatusEnum::class,
            'printed_at' => 'immutable_datetime',
        ]);
    }
}
