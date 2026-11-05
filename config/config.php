<?php

declare(strict_types=1);

return [
<<<<<<< .merge_file_Yg9L4N
<<<<<<< HEAD
=======
    'navigation_sort' => 5,
>>>>>>> laraxot/dev
=======
    'navigation_sort' => 5,
>>>>>>> .merge_file_QeWgWe
    'name' => 'Labeling',
    'description' => 'Etichette, identificatori e stampanti per i modelli Labelable',
    'icon' => 'heroicon-o-qr-code',
    'navigation' => [
        'enabled' => true,
        'sort' => 30,
    ],

    /*
     * Modelli etichettabili (Model & LabelableContract) proposti nel campo "tipo di modello"
     * dei template. Ogni modulo consumer si registra dal proprio ServiceProvider:
     * config()->push('labeling.labelables', StockUnit::class).
     */
    'labelables' => [],

    /*
     * Codice della stampante usata quando PrintLabelAction riceve $printer = null, per formato
     * del template. Senza stampante un template ZPL resta "rendered" (snapshot salvato, nulla
     * inviato); un template PDF produce comunque il file.
     */
    'default_printers' => [
        'zpl' => env('LABELING_DEFAULT_ZPL_PRINTER'),
        'pdf' => env('LABELING_DEFAULT_PDF_PRINTER'),
    ],

    // Socket raw (porta 9100) verso le stampanti ZPL, in secondi.
    'zpl_tcp' => [
        'connect_timeout' => 3.0,
        'write_timeout' => 5.0,
    ],

    // File PDF prodotti dal driver pdf_download.
    'pdf' => [
        'disk' => 'local',
        'directory' => 'labeling/prints',
    ],

    // Lato in pixel dell'SVG del QR passato ai template PDF come $qrSvg.
    'qr_size_px' => 256,

    'max_copies' => 100,
];
