<?php

declare(strict_types=1);

namespace Modules\Labeling\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Linguaggio del contenuto del template. Etichette: lang/{locale}/label_format_enum.php.
 */
enum LabelFormatEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case ZPL = 'zpl';
    case PDF = 'pdf';
}
