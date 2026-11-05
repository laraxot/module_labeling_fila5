<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Modules\Labeling\Datas\NormalizedScanData;
use Spatie\QueueableAction\QueueableAction;

/**
 * Ripulisce una lettura grezza prima della ricerca. Funzione pura (testata in unit).
 *
 * - toglie i caratteri di controllo che i lettori keyboard-wedge accodano (CR, LF, TAB, prefissi STX/ETX) e gli spazi;
 * - se la lettura e' l'URL del QR (`https://host/l/{code}` o `/l/{code}`) estrae `{code}` decodificato;
 * - se e' un EPC/UID esadecimale (anche con separatori `:`, `-` o spazi) ne calcola la forma maiuscola compatta.
 */
class NormalizeScannedCodeAction
{
    use QueueableAction;

    /** Lunghezza minima, in cifre esadecimali, di un UID NFC (4 byte) o EPC RFID. */
    private const MIN_HEX_LENGTH = 8;

    public function execute(string $scanned): NormalizedScanData
    {
        $clean = preg_replace('/[\x00-\x1F\x7F]+/', '', $scanned) ?? $scanned;
        $clean = trim($clean);

        $fromUrl = false;
        if (str_contains($clean, '://') || str_starts_with($clean, '/')) {
            if (preg_match('~/l/([^/?#]+)/?(?:[?#].*)?$~', $clean, $matches) === 1) {
                $clean = trim(rawurldecode($matches[1]));
                $fromUrl = true;
            }
        }

        return new NormalizedScanData(
            raw: $scanned,
            code: $clean,
            hex: $fromUrl ? null : $this->hex($clean),
            fromUrl: $fromUrl,
        );
    }

    private function hex(string $value): ?string
    {
        $compact = preg_replace('/[\s:\-]+/', '', $value) ?? $value;

        if (strlen($compact) < self::MIN_HEX_LENGTH || strlen($compact) % 2 !== 0) {
            return null;
        }

        if (preg_match('/^[0-9A-Fa-f]+$/', $compact) !== 1) {
            return null;
        }

        return strtoupper($compact);
    }
}
