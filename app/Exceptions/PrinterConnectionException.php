<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

use Modules\Labeling\Models\Printer;

class PrinterConnectionException extends LabelingException
{
    public static function unreachable(Printer $printer, string $reason, int $code = 0): self
    {
        return new self((string) __('labeling::exceptions.printer_unreachable', [
            'printer' => $printer->code,
            'host' => (string) $printer->host,
            'port' => $printer->port,
            'reason' => $reason,
        ]), $code);
    }

    public static function missingHost(Printer $printer): self
    {
        return new self((string) __('labeling::exceptions.printer_missing_host', [
            'printer' => $printer->code,
        ]));
    }

    public static function writeFailed(Printer $printer): self
    {
        return new self((string) __('labeling::exceptions.printer_write_failed', [
            'printer' => $printer->code,
        ]));
    }
}
