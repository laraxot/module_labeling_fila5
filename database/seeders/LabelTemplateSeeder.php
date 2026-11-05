<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Models\LabelTemplate;

/**
 * Due template generici di default (`labelable_type` null): ZPL 100x50 mm per stampanti termiche
 * e PDF 100x50 mm per stampa da ufficio. I moduli consumer aggiungono i propri per classe.
 */
class LabelTemplateSeeder extends Seeder
{
    /** QR con l'URL `/l/{code}` a sinistra, codice e prime righe di `$data` a destra. */
    public const ZPL_DEFAULT = <<<'ZPL'
^XA
^CI28
^PW{{ $widthDots }}
^LL{{ $heightDots }}
^FO24,24^BQN,2,6^FDQA,{{ $url }}^FS
^FO300,40^A0N,44,44^FD{{ $code }}^FS
@foreach (array_slice($data, 0, 4, true) as $key => $value)
^FO300,{{ 110 + $loop->index * 44 }}^A0N,30,30^FD{{ $value }}^FS
@endforeach
^XZ
ZPL;

    public const PDF_DEFAULT = <<<'HTML'
<page backtop="3mm" backbottom="3mm" backleft="3mm" backright="3mm">
<table style="width: 100%;">
    <tr>
        <td style="width: 40%;">{!! $qrSvg !!}</td>
        <td style="width: 60%; vertical-align: top;">
            <div style="font-size: 16pt; font-weight: bold;">{{ $code }}</div>
            @foreach (array_slice($data, 0, 4, true) as $key => $value)
                <div style="font-size: 9pt;">{{ $value }}</div>
            @endforeach
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
