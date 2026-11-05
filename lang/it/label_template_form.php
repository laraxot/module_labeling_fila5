<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Codice', 'placeholder' => 'Es. SU-ZPL-100X50', 'helper_text' => 'Identificativo univoco del template'],
        'name' => ['label' => 'Nome', 'placeholder' => 'Es. Unita\' di stoccaggio 100x50', 'helper_text' => ''],
        'format' => ['label' => 'Formato', 'placeholder' => 'ZPL o PDF', 'helper_text' => 'ZPL per stampanti termiche Zebra, PDF per stampa da ufficio'],
        'labelable_type' => ['label' => 'Tipo di elemento', 'placeholder' => 'Tutti', 'helper_text' => 'Vuoto: template generico. I tipi disponibili sono registrati dai moduli (config labeling.labelables)'],
        'width_mm' => ['label' => 'Larghezza (mm)', 'placeholder' => '100', 'helper_text' => ''],
        'height_mm' => ['label' => 'Altezza (mm)', 'placeholder' => '50', 'helper_text' => ''],
        'dpi' => ['label' => 'Risoluzione (dpi)', 'placeholder' => '203', 'helper_text' => 'Risoluzione della testina: 203 dpi = 8 punti/mm'],
        'is_default' => ['label' => 'Predefinito', 'placeholder' => '', 'helper_text' => 'Usato quando la stampa non indica un template'],
        'is_active' => ['label' => 'Attivo', 'placeholder' => '', 'helper_text' => ''],
        'content' => [
            'label' => 'Contenuto',
            'placeholder' => '^XA ... ^XZ',
            'helper_text' => 'Blade. Variabili: $code, $url, $data, $widthMm, $heightMm, $dpi, $widthDots, $heightDots; nei PDF anche $qrSvg (usare {!! $qrSvg !!}).',
        ],
    ],
];
