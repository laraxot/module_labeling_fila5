<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

use Modules\Labeling\Models\Printer;

class PrinterTimeoutException extends LabelingException
{
    public static function whileWriting(Printer $printer, float $seconds): self
    {
        return new self((string) __('labeling::exceptions.printer_timeout', [
            'printer' => $printer->code,
            'seconds' => $seconds,
        ]));
    }
}
