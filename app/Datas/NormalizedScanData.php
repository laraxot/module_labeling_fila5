<?php

declare(strict_types=1);

namespace Modules\Labeling\Datas;

use Spatie\LaravelData\Data;

/**
 * Esito della normalizzazione di una lettura (lettore keyboard-wedge, fotocamera, RFID).
 *
 * - `code`: valore ripulito (trim, caratteri di controllo rimossi, codice estratto da `.../l/{code}`);
 * - `hex`: lo stesso valore in esadecimale maiuscolo senza separatori, se la lettura e' un EPC/UID
 *   (`04:a2:1b...` → `04A21B...`), altrimenti null;
 * - `fromUrl`: la lettura era l'URL del QR.
 */
class NormalizedScanData extends Data
{
    public function __construct(
        public string $raw,
        public string $code,
        public ?string $hex = null,
        public bool $fromUrl = false,
    ) {}

    public function isEmpty(): bool
    {
        return $this->code === '';
    }
}
