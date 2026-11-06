<?php

declare(strict_types=1);

$allowedPrinterHosts = config('env.LABELING_ALLOWED_PRINTER_HOSTS');
$allowedPrinterHosts = is_string($allowedPrinterHosts) ? $allowedPrinterHosts : '';

return [
    'navigation_sort' => 5,
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
     * del template. Senza stampante un template ZPL fallisce (stampa `failed` con il motivo, errore
     * notificato all'operatore); un template PDF produce comunque il file.
     */
    'default_printers' => [
        'zpl' => config('env.LABELING_DEFAULT_ZPL_PRINTER'),
        'pdf' => config('env.LABELING_DEFAULT_PDF_PRINTER'),
    ],

    /*
     * Indirizzi a cui il server puo' collegarsi per stampare (EnsurePrinterAddressAllowedAction).
     * Vuota: solo IP privati di LAN (10/8, 172.16/12, 192.168/16, fc00::/7) e hostname che si
     * risolvono li'; niente loopback ne' indirizzi pubblici. Valorizzata: solo le voci elencate,
     * IP, CIDR (es. '192.168.10.0/24'), hostname esatti o '*.dominio'. Metadata cloud e link-local
     * restano sempre vietati.
     */
    'allowed_printer_hosts' => array_values(array_filter(array_map('trim', explode(',', $allowedPrinterHosts)))),

    // Porte ammesse per le stampanti di rete (9100 raw Zebra, 6101 alternativa).
    'printer_ports' => [
        'min' => 1024,
        'max' => 65535,
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

    // Lato in pixel dell'SVG del QR inserito nei template PDF con {{ qr_svg }}.
    'qr_size_px' => 256,

    'max_copies' => 100,
];
