<?php

declare(strict_types=1);

namespace Modules\Labeling\Filament\Actions;

use Modules\Labeling\Filament\Actions\Concerns\ConfiguresPrintLabelAction;
use Modules\Xot\Filament\Actions\XotBaseAction;

/**
 * Azione di riga "Stampa etichetta" per le tabelle di modelli Labelable.
 *
 * ```php
 * public function getTableActions(): array
 * {
 *     return [...parent::getTableActions(), 'print_label' => PrintLabelTableAction::make()];
 * }
 * ```
 *
 * Lang: Modules/Labeling/lang/{locale}/print_label_table.php.
 */
class PrintLabelTableAction extends XotBaseAction
{
    use ConfiguresPrintLabelAction;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurePrintLabel();
        $this->iconButton();
    }
}
