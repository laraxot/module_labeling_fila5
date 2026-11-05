<?php

declare(strict_types=1);

namespace Modules\Labeling\Drivers;

use Illuminate\Support\Facades\Storage;
use Modules\Labeling\Contracts\PrinterDriverContract;
use Modules\Labeling\Datas\PrinterDeliveryData;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Exceptions\LabelRenderException;
use Modules\Labeling\Models\LabelPrint;
use Modules\Labeling\Models\LabelTemplate;
use Spipu\Html2Pdf\Html2Pdf;
use Webmozart\Assert\Assert;

/**
 * Produce il PDF dell'etichetta (una pagina per copia, formato = dimensioni del template in mm)
 * e lo salva sul disco di `config('labeling.pdf.disk')`. Il path finisce in `label_prints.output_path`.
 *
 * Si usa spipu/html2pdf direttamente (gia' dipendenza di Xot) perche' GeneratePdfAction di Xot
 * fissa il formato A4.
 */
class PdfDownloadPrinterDriver implements PrinterDriverContract
{
    public function deliver(LabelPrint $print): PrinterDeliveryData
    {
        $template = $print->labelTemplate;
        Assert::isInstanceOf($template, LabelTemplate::class);

        $page = str_contains($print->rendered, '<page') ? $print->rendered : '<page>'.$print->rendered.'</page>';
        $html = str_repeat($page, max(1, $print->copies));

        try {
            $pdf = new Html2Pdf(
                orientation: $template->width_mm > $template->height_mm ? 'L' : 'P',
                format: [$template->width_mm, $template->height_mm],
                lang: in_array(app()->getLocale(), ['it', 'en', 'de', 'fr', 'es'], true) ? app()->getLocale() : 'en',
                unicode: true,
                encoding: 'UTF-8',
                margins: [0, 0, 0, 0],
            );
            $pdf->writeHTML($html);
            $content = $pdf->output('', 'S');
        } catch (\Throwable $e) {
            throw LabelRenderException::pdf($e);
        }
        Assert::string($content);

        $disk = config()->string('labeling.pdf.disk', 'local');
        $path = trim(config()->string('labeling.pdf.directory', 'labeling/prints'), '/')
            .'/'.sprintf('label-print-%d-%s.pdf', $print->id, now()->format('YmdHis'));

        if (! Storage::disk($disk)->put($path, $content)) {
            throw LabelRenderException::pdf(new \RuntimeException('Unable to write '.$path.' on disk '.$disk));
        }

        return new PrinterDeliveryData(status: LabelPrintStatusEnum::RENDERED, bytes: strlen($content), path: $path);
    }
}
