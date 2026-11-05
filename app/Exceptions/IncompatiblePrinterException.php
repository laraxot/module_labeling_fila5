<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

use Modules\Labeling\Models\LabelTemplate;
use Modules\Labeling\Models\Printer;

class IncompatiblePrinterException extends LabelingException
{
    public static function for(Printer $printer, LabelTemplate $template): self
    {
        return new self((string) __('labeling::exceptions.incompatible_printer', [
            'printer' => $printer->code,
            'driver' => $printer->driver->getLabel(),
            'template' => $template->code,
            'format' => $template->format->getLabel(),
        ]));
    }

    public static function inactive(Printer $printer): self
    {
        return new self((string) __('labeling::exceptions.printer_inactive', [
            'printer' => $printer->code,
        ]));
    }
}
