<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Models\Printer;
use Spatie\QueueableAction\QueueableAction;

/**
 * Stampante di default per un formato, dal codice in `config('labeling.default_printers.{format}')`.
 * Null se non configurata o non attiva.
 */
class ResolveDefaultPrinterAction
{
    use QueueableAction;

    public function execute(LabelFormatEnum $format): ?Printer
    {
        $code = config('labeling.default_printers.'.$format->value);
        if (! is_string($code) || $code === '') {
            return null;
        }

        return Printer::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }
}
