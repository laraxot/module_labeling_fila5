<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Models\LabelTemplate;

/**
 * Due template generici di default (`labelable_type` null): ZPL 100x50 mm per stampanti termiche
 * e PDF 100x50 mm per stampa da ufficio. I moduli consumer aggiungono i propri per classe.
 *
 * I template sono testo con segnaposto `{{ chiave }}` ({@see \Modules\Labeling\Actions\RenderLabelAction}):
 * nessuna logica. Le prime quattro righe di dati sono `line_1` ... `line_4` (i valori di `getLabelData()`
 * in ordine), cosi' lo stesso template va bene per ogni modello. `updateOrCreate` riscrive anche i
 * template di default salvati con la vecchia sintassi Blade.
 */
class LabelTemplateSeeder extends Seeder
{
    /** QR con l'URL `/l/{code}` a sinistra, codice e prime quattro righe di dati a destra. */
    public const string ZPL_DEFAULT = <<<'ZPL'
^XA
^CI28
^PW{{ width_dots }}
^LL{{ height_dots }}
^FO24,24^BQN,2,6^FDQA,{{ url }}^FS
^FO300,40^A0N,44,44^FD{{ code }}^FS
^FO300,110^A0N,30,30^FD{{ line_1 }}^FS
^FO300,154^A0N,30,30^FD{{ line_2 }}^FS
^FO300,198^A0N,30,30^FD{{ line_3 }}^FS
^FO300,242^A0N,30,30^FD{{ line_4 }}^FS
^XZ
ZPL;

    public const string PDF_DEFAULT = <<<'HTML'
<page backtop="3mm" backbottom="3mm" backleft="3mm" backright="3mm">
<table style="width: 100%;">
    <tr>
        <td style="width: 40%;">{{ qr_svg }}</td>
        <td style="width: 60%; vertical-align: top;">
            <div style="font-size: 16pt; font-weight: bold;">{{ code }}</div>
            <div style="font-size: 9pt;">{{ line_1 }}</div>
            <div style="font-size: 9pt;">{{ line_2 }}</div>
            <div style="font-size: 9pt;">{{ line_3 }}</div>
            <div style="font-size: 9pt;">{{ line_4 }}</div>
        </td>
    </tr>
</table>
</page>
HTML;

    public function run(): void
    {
        LabelTemplate::query()->updateOrCreate(['code' => 'DEFAULT-ZPL-100X50'], [
            'name' => 'Default ZPL 100x50',
            'format' => LabelFormatEnum::ZPL,
            'width_mm' => 100,
            'height_mm' => 50,
            'dpi' => 203,
            'content' => self::ZPL_DEFAULT,
            'labelable_type' => null,
            'is_default' => true,
            'is_active' => true,
        ]);

        LabelTemplate::query()->updateOrCreate(['code' => 'DEFAULT-PDF-100X50'], [
            'name' => 'Default PDF 100x50',
            'format' => LabelFormatEnum::PDF,
            'width_mm' => 100,
            'height_mm' => 50,
            'dpi' => 203,
            'content' => self::PDF_DEFAULT,
            'labelable_type' => null,
            'is_default' => false,
            'is_active' => true,
        ]);
    }
}
