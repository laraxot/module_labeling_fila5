<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Codice', 'placeholder' => 'Es. ZEBRA-INGRESSO', 'helper_text' => 'Usato anche in config labeling.default_printers'],
        'name' => ['label' => 'Nome', 'placeholder' => 'Es. Zebra ZT411 pesa ingresso', 'helper_text' => ''],
        'driver' => ['label' => 'Tipo di collegamento', 'placeholder' => '', 'helper_text' => ''],
        'dpi' => ['label' => 'Risoluzione (dpi)', 'placeholder' => '203', 'helper_text' => ''],
        'host' => ['label' => 'Indirizzo IP / host', 'placeholder' => 'Es. 192.168.1.50', 'helper_text' => 'Indirizzo della rete locale (10.x, 172.16-31.x, 192.168.x) o nome di rete che si risolve li\'; altri indirizzi solo se elencati in labeling.allowed_printer_hosts'],
        'port' => ['label' => 'Porta', 'placeholder' => '9100', 'helper_text' => 'Porta raw della stampante, di solito 9100'],
        'is_active' => ['label' => 'Attiva', 'placeholder' => '', 'helper_text' => ''],
    ],
];
