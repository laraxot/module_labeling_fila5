<?php

declare(strict_types=1);

namespace Modules\Labeling\Datas;

use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Spatie\LaravelData\Data;

/**
 * Risultato della consegna di una stampa da parte di un driver.
 */
class PrinterDeliveryData extends Data
{
    public function __construct(
        public LabelPrintStatusEnum $status,
        public int $bytes = 0,
        public ?string $path = null,
    ) {}
}
