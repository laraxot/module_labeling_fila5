<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Modules\Labeling\Enums\LabelFormatEnum;
use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_match;
use function Safe\preg_match_all;

/**
 * Controlla il contenuto di un template di etichetta prima di salvarlo (form) e prima di ogni
 * render ({@see RenderLabelAction}). Restituisce il messaggio d'errore tradotto, null se valido.
 *
 * Il template e' testo con segnaposto chiusi `{{ chiave }}`, mai codice:
 * - niente tag PHP (`<?`), echo grezzi Blade (`{!! !!}`) ne' direttive (`@php`, `@foreach`, ...);
 * - ogni `{{ ... }}` deve essere una chiave nota ({@see self::FIXED_KEYS}, `line_N`, `data.<chiave>`,
 *   `qr_svg` solo nei PDF);
 * - nei PDF niente risorse esterne o locali (`<img>`, `src=`, `href=`, `url(`, `<script>`, ...):
 *   Html2Pdf le caricherebbe dal server. L'unica immagine e' il QR generato (`{{ qr_svg }}`).
 */
class ValidateLabelTemplateContentAction
{
    use QueueableAction;

    /** Segnaposto sempre disponibili, in entrambi i formati. */
    public const array FIXED_KEYS = ['code', 'url', 'width_mm', 'height_mm', 'dpi', 'width_dots', 'height_dots'];

    /** Segnaposto disponibile solo nei template PDF: SVG del QR, inserito senza escape. */
    public const string QR_SVG_KEY = 'qr_svg';

    /** Qualunque `{{ ... }}`: il contenuto si valida a parte. */
    public const string PLACEHOLDER_PATTERN = '/\{\{(.*?)\}\}/s';

    /** `@parola` non preceduto da lettera, cifra, punto o `@`: una direttiva Blade (un'email non lo e'). */
    private const string DIRECTIVE_PATTERN = '/(?<![\w.@])@[A-Za-z]+/';

    /** Elementi o attributi con cui Html2Pdf carica file o URL. */
    private const string PDF_EXTERNAL_RESOURCE_PATTERN = '/<\s*(?:img|link|script|iframe|object|embed|base|meta)\b|\b(?:src|href|background)\s*=|url\s*\(/i';

    public function execute(string $content, LabelFormatEnum $format): ?string
    {
        if (str_contains($content, '<?')) {
            return $this->message('php_tag');
        }

        if (str_contains($content, '{!!') || str_contains($content, '!!}')) {
            return $this->message('raw_echo');
        }

        if (preg_match(self::DIRECTIVE_PATTERN, $content, $directive) === 1 && isset($directive[0]) && is_string($directive[0])) {
            return $this->message('directive', ['directive' => $directive[0]]);
        }

        preg_match_all(self::PLACEHOLDER_PATTERN, $content, $matches);
        $inners = is_array($matches) && is_array($matches[1] ?? null) ? $matches[1] : [];
        foreach ($inners as $inner) {
            $key = is_string($inner) ? trim($inner) : '';
            if (! self::isKnownKey($key, $format)) {
                return $this->message('unknown_placeholder', ['placeholder' => '{{ '.mb_substr($key, 0, 60).' }}']);
            }
        }

        if ($format === LabelFormatEnum::PDF && preg_match(self::PDF_EXTERNAL_RESOURCE_PATTERN, $content, $resource) === 1 && isset($resource[0]) && is_string($resource[0])) {
            return $this->message('external_resource', ['resource' => trim($resource[0])]);
        }

        return null;
    }

    public static function isKnownKey(string $key, LabelFormatEnum $format): bool
    {
        return in_array($key, self::FIXED_KEYS, true)
            || ($format === LabelFormatEnum::PDF && $key === self::QR_SVG_KEY)
            || preg_match('/^line_[1-9]\d?$/', $key) === 1
            || preg_match('/^data\.[A-Za-z0-9_]+$/', $key) === 1;
    }

    /**
     * @param  array<string, string>  $replace
     */
    private function message(string $key, array $replace = []): string
    {
        return (string) __('labeling::exceptions.template_content.'.$key, $replace);
    }
}
