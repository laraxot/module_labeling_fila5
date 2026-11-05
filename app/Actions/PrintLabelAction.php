<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Contracts\PrinterDriverContract;
use Modules\Labeling\Drivers\PdfDownloadPrinterDriver;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Exceptions\IncompatiblePrinterException;
use Modules\Labeling\Exceptions\LabelingException;
use Modules\Labeling\Exceptions\PrinterNotConfiguredException;
use Modules\Labeling\Models\LabelPrint;
use Modules\Labeling\Models\LabelTemplate;
use Modules\Labeling\Models\Printer;
use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * Stampa (o accoda con `->onQueue()->execute(...)`) l'etichetta di un modello.
 *
 * 1. template: quello passato oppure {@see ResolveLabelTemplateAction};
 * 2. stampante: quella passata oppure il default del formato ({@see ResolveDefaultPrinterAction}),
 *    che deve essere attiva e compatibile con il formato del template;
 * 3. render ({@see RenderLabelAction}) e snapshot su `label_prints` in stato `queued`;
 * 4. consegna con il driver della stampante; un template PDF senza stampante produce comunque il file.
 *
 * Ogni fallimento dopo la scelta del template e' visibile: la stampa resta `failed` con il motivo in
 * `error` (storico in LabelPrintResource, anche per le stampe in coda) e l'eccezione di dominio si
 * rilancia, cosi' la UI la notifica e la coda segna il job fallito. Casi:
 * - template ZPL senza stampante ({@see PrinterNotConfiguredException}): prima restava `rendered`
 *   e l'operatore leggeva "etichetta in stampa" senza che uscisse nulla;
 * - template non valido ({@see RenderLabelAction});
 * - errore del driver (stampante irraggiungibile, indirizzo non consentito, PDF non scritto).
 *
 * @throws LabelingException
 */
class PrintLabelAction
{
    use QueueableAction;

    public function execute(Model&LabelableContract $model, ?LabelTemplate $template = null, ?Printer $printer = null, int $copies = 1): LabelPrint
    {
        Assert::range($copies, 1, config()->integer('labeling.max_copies', 100));

        $template ??= app(ResolveLabelTemplateAction::class)->execute($model);
        Assert::true($template->is_active, (string) __('labeling::exceptions.template_inactive', ['template' => $template->code]));

        $printer ??= app(ResolveDefaultPrinterAction::class)->execute($template->format);
        if ($printer !== null) {
            if (! $printer->is_active) {
                throw IncompatiblePrinterException::inactive($printer);
            }
            if ($printer->driver->format() !== $template->format) {
                throw IncompatiblePrinterException::for($printer, $template);
            }
        }

        $print = new LabelPrint([
            'label_template_id' => $template->id,
            'printer_id' => $printer?->id,
            'copies' => $copies,
            'rendered' => '',
            'status' => LabelPrintStatusEnum::QUEUED,
        ]);
        $print->labelable()->associate($model);
        $print->setRelation('labelTemplate', $template);
        $print->setRelation('printer', $printer);

        try {
            $print->rendered = app(RenderLabelAction::class)->execute($model, $template);
            $print->save();

            $driverClass = $printer?->driver->driverClass()
                ?? ($template->format === LabelFormatEnum::PDF ? PdfDownloadPrinterDriver::class : null);
            if ($driverClass === null) {
                throw PrinterNotConfiguredException::for($template);
            }

            $driver = app($driverClass);
            Assert::isInstanceOf($driver, PrinterDriverContract::class);

            $delivery = $driver->deliver($print);
        } catch (LabelingException $e) {
            $print->error = $e->getMessage();
            $this->transition($print, LabelPrintStatusEnum::FAILED);

            throw $e;
        }

        $print->output_path = $delivery->path;

        return $this->transition($print, $delivery->status);
    }

    private function transition(LabelPrint $print, LabelPrintStatusEnum $to): LabelPrint
    {
        Assert::true($print->status->canTransitionTo($to));

        $print->status = $to;
        if ($to !== LabelPrintStatusEnum::FAILED) {
            $print->printed_at = now()->toImmutable();
        }
        $print->save();

        return $print;
    }
}
