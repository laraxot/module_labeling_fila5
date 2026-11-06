<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Exceptions\LabelRenderException;
use Modules\Labeling\Models\LabelTemplate;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

use function Safe\preg_match;
use function Safe\preg_replace_callback;

/**
 * Renderizza il template di un'etichetta sostituendo segnaposto chiusi `{{ chiave }}`.
 *
 * Il contenuto del template si modifica dal pannello: non e' mai compilato ne' eseguito (niente Blade),
 * e prima del render passa da {@see ValidateLabelTemplateContentAction}. Chiavi:
 * - `code`, `url` (payload del QR), `width_mm`, `height_mm`, `dpi`, `width_dots`, `height_dots`;
 * - `data.<chiave>`: un valore di `getLabelData()` (vuoto se il modello non ha quella chiave);
 * - `line_1` ... `line_N`: i valori di `getLabelData()` nell'ordine, per i template generici;
 * - solo PDF: `qr_svg`, l'SVG del QR, inserito cosi' com'e'.
 *
 * Escape: nei PDF ogni valore passa da `htmlspecialchars` (il template e' HTML); nei ZPL i valori non
 * si toccano (un `&` resta `&`) ma `^` e `~` diventano spazi, cosi' un dato non puo' iniettare comandi
 * nella stampante. La sostituzione e' a passata unica: un valore che contiene `{{ x }}` resta testo.
 *
 * @throws LabelRenderException
 */
class RenderLabelAction
{
    use QueueableAction;

    public function execute(Model&LabelableContract $model, LabelTemplate $template): string
    {
        $error = app(ValidateLabelTemplateContentAction::class)->execute($template->content, $template->format);
        if ($error !== null) {
            throw LabelRenderException::invalidContent($template->code, $error);
        }

        $isPdf = $template->format === LabelFormatEnum::PDF;
        $code = $model->getLabelCode();
        $url = app(BuildLabelUrlAction::class)->execute($code);
        $data = $model->getLabelData();

        /** @var array<string, string|int|float|null> $values */
        $values = [
            'code' => $code,
            'url' => $url,
            'width_mm' => $template->width_mm,
            'height_mm' => $template->height_mm,
            'dpi' => $template->dpi,
            'width_dots' => $template->mmToDots($template->width_mm),
            'height_dots' => $template->mmToDots($template->height_mm),
        ];
        $line = 0;
        foreach ($data as $key => $value) {
            $values['data.'.$key] = $value;
            $values['line_'.++$line] = $value;
        }

        $replacements = array_map(
            fn (string|int|float|null $value): string => $isPdf ? $this->htmlSafe($value) : $this->zplSafe($value),
            $values,
        );

        if ($isPdf && $this->usesKey($template->content, ValidateLabelTemplateContentAction::QR_SVG_KEY)) {
            $replacements[ValidateLabelTemplateContentAction::QR_SVG_KEY] = app(GenerateQrSvgAction::class)
                ->execute($url, config()->integer('labeling.qr_size_px', 256));
        }

        $rendered = preg_replace_callback(
            ValidateLabelTemplateContentAction::PLACEHOLDER_PATTERN,
            static function (array $match) use ($replacements): string {
                $key = $match[1] ?? null;

                return is_string($key) ? ($replacements[trim($key)] ?? '') : '';
            },
            $template->content,
        );

        Assert::string($rendered);

        return $rendered;
    }

    private function usesKey(string $content, string $key): bool
    {
        return preg_match('/\{\{\s*'.preg_quote($key, '/').'\s*\}\}/', $content) === 1;
    }

    private function htmlSafe(string|int|float|null $value): string
    {
        return htmlspecialchars($this->stringify($value), ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    private function zplSafe(string|int|float|null $value): string
    {
        return str_replace(['^', '~'], ' ', $this->stringify($value));
    }

    private function stringify(string|int|float|null $value): string
    {
        return $value === null ? '' : (string) $value;
    }
}
