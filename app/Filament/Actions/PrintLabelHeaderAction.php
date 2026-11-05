<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Actions;

use Modules\Labeling\Filament\Actions\Concerns\ConfiguresPrintLabelAction;
use Modules\Xot\Filament\Actions\XotBaseAction;

/**
 * Header action "Stampa etichetta" per le pagine View/Edit di modelli Labelable
 * (il record arriva dalla pagina).
 *
 * ```php
 * protected function getHeaderActions(): array
 * {
 *     return [...parent::getHeaderActions(), 'print_label' => PrintLabelHeaderAction::make()];
 * }
 * ```
 *
 * Lang: Modules/Labeling/lang/{locale}/print_label_header.php.
 */
class PrintLabelHeaderAction extends XotBaseAction
{
    use ConfiguresPrintLabelAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurePrintLabel();
        $this->button();
    }
}
