<?php

declare(strict_types=1);

namespace Modules\Labeling\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Supporto fisico dell'identificatore. Etichetta/colore/icona: lang/{locale}/identifier_type_enum.php.
 */
enum IdentifierTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case QR = 'qr';
    case BARCODE128 = 'barcode128';
    case RFID_UHF = 'rfid_uhf';
    case NFC = 'nfc';

    /** QR e Code128 si stampano: il valore di default e' il codice dell'etichetta. */
    public function isPrinted(): bool
    {
        return match ($this) {
            self::QR, self::BARCODE128 => true,
            self::RFID_UHF, self::NFC => false,
        };
    }

    /** RFID EPC e UID NFC sono esadecimali: si confrontano normalizzati in maiuscolo. */
    public function isHexadecimal(): bool
    {
        return ! $this->isPrinted();
    }
}
