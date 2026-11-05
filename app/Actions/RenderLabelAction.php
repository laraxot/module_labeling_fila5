<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Exceptions\LabelingException;
use Modules\Labeling\Exceptions\LabelRenderException;
use Modules\Labeling\Models\LabelTemplate;
use Spatie\QueueableAction\QueueableAction;

/**
 * Renderizza il contenuto Blade del template per un modello.
 *
 * Variabili: `$code`, `$url` (payload del QR), `$data` (getLabelData()), `$widthMm`, `$heightMm`,
 * `$dpi`, `$widthDots`, `$heightDots`; per i PDF anche `$qrSvg`.
 *
 * - PDF: escape HTML standard di Blade (`{{ }}`), `{!! $qrSvg !!}` per l'SVG;
 * - ZPL: `{{ }}` stampa il valore senza escape HTML (un `&` deve restare `&`), mentre i caratteri
 *   di comando ZPL `^` e `~` nei valori del modello si sostituiscono con uno spazio, cosi' un dato
 *   non puo' iniettare comandi nella stampante.
 *
 * @throws LabelRenderException
 */
class RenderLabelAction
{
    use QueueableAction;

    /** Formato echo Blade senza escape HTML, usato solo per i template ZPL. */
    private const ZPL_ECHO_FORMAT = '%s';

    public function execute(Model&LabelableContract $model, LabelTemplate $template): string
    {
        $code = $model->getLabelCode();
        $url = app(BuildLabelUrlAction::class)->execute($code);
        $data = $model->getLabelData();

        $variables = [
            'template' => $template,
            'widthMm' => $template->width_mm,
            'heightMm' => $template->height_mm,
            'dpi' => $template->dpi,
            'widthDots' => $template->mmToDots($template->width_mm),
            'heightDots' => $template->mmToDots($template->height_mm),
        ];

        try {
            if ($template->format === LabelFormatEnum::PDF) {
                return Blade::render($template->content, [
                    ...$variables,
                    'code' => $code,
                    'url' => $url,
                    'data' => $data,
                    'qrSvg' => app(GenerateQrSvgAction::class)->execute($url, config()->integer('labeling.qr_size_px', 256)),
                ]);
            }

            $zplVariables = [
                ...$variables,
                'code' => $this->zplSafe($code),
                'url' => $this->zplSafe($url),
                'data' => array_map($this->zplSafe(...), $data),
            ];

            return Blade::usingEchoFormat(
                self::ZPL_ECHO_FORMAT,
                static fn (): string => Blade::render($template->content, $zplVariables),
            );
        } catch (LabelingException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw LabelRenderException::template($template->code, $e);
        }
    }

    private function zplSafe(string|int|float|null $value): string|int|float|null
    {
        return is_string($value) ? str_replace(['^', '~'], ' ', $value) : $value;
    }
}
