<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

use Modules\Labeling\Models\Printer;

class PrinterConnectionException extends LabelingException
{
    /**
     * Il motivo tecnico (errore del socket) non va all'operatore: distinguerebbe porte chiuse, filtrate
     * o host assenti. Il driver lo scrive nel log.
     */
    public static function unreachable(Printer $printer, int $code = 0): self
    {
        return new self((string) __('labeling::exceptions.printer_unreachable', [
            'printer' => $printer->code,
            'host' => (string) $printer->host,
            'port' => (string) $printer->port,
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
