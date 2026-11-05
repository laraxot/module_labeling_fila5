<?php

declare(strict_types=1);

namespace Modules\Labeling\Exceptions;

use Modules\Labeling\Models\LabelTemplate;

/**
 * Nessuna stampante per un template che ne richiede una (ZPL): la stampa non puo' andare da nessuna parte.
 */
class PrinterNotConfiguredException extends LabelingException
{
    public static function for(LabelTemplate $template): self
    {
        return new self((string) __('labeling::exceptions.printer_not_configured', [
            'template' => $template->code,
            'format' => $template->format->getLabel(),
            'key' => 'labeling.default_printers.'.$template->format->value,
        ]));
    }
}
