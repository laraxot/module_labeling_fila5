<?php

declare(strict_types=1);

namespace Modules\Labeling\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

/**
 * Esito di una richiesta di stampa. Etichette: lang/{locale}/label_print_status_enum.php.
 *
 * - queued: snapshot salvato, consegna non ancora tentata;
 * - sent: inviato alla stampante (ZPL via TCP);
 * - rendered: file PDF prodotto su disco (`output_path`), da scaricare;
 * - failed: render o consegna falliti, oppure nessuna stampante per un template ZPL; il motivo e' in `error`.
 */
enum LabelPrintStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case QUEUED = 'queued';
    case SENT = 'sent';
    case RENDERED = 'rendered';
    case FAILED = 'failed';

    public function canTransitionTo(self $to): bool
    {
        return match ($this) {
            self::QUEUED => in_array($to, [self::SENT, self::RENDERED, self::FAILED], true),
            self::RENDERED => $to === self::SENT,
            self::SENT, self::FAILED => false,
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::SENT, self::FAILED], true);
    }
}
