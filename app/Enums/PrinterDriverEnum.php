<?php

declare(strict_types=1);

namespace Modules\Labeling\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Labeling\Contracts\PrinterDriverContract;
use Modules\Labeling\Drivers\PdfDownloadPrinterDriver;
use Modules\Labeling\Drivers\ZplTcpPrinterDriver;
use Modules\Xot\Traits\EnumTrait;

/**
 * Come una stampante riceve l'etichetta. Etichette: lang/{locale}/printer_driver_enum.php.
 */
enum PrinterDriverEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case ZPL_TCP = 'zpl_tcp';
    case PDF_DOWNLOAD = 'pdf_download';

    /** @return class-string<PrinterDriverContract> */
    public function driverClass(): string
    {
        return match ($this) {
            self::ZPL_TCP => ZplTcpPrinterDriver::class,
            self::PDF_DOWNLOAD => PdfDownloadPrinterDriver::class,
        };
    }

    /** Formato di template che il driver sa consegnare. */
    public function format(): LabelFormatEnum
    {
        return match ($this) {
            self::ZPL_TCP => LabelFormatEnum::ZPL,
            self::PDF_DOWNLOAD => LabelFormatEnum::PDF,
        };
    }

    public function requiresHost(): bool
    {
        return $this === self::ZPL_TCP;
    }
}
