<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Code', 'placeholder' => 'E.g. ZEBRA-GATE', 'helper_text' => 'Also used in config labeling.default_printers'],
        'name' => ['label' => 'Name', 'placeholder' => 'E.g. Zebra ZT411 inbound scale', 'helper_text' => ''],
        'driver' => ['label' => 'Connection', 'placeholder' => '', 'helper_text' => ''],
        'dpi' => ['label' => 'Resolution (dpi)', 'placeholder' => '203', 'helper_text' => ''],
        'host' => ['label' => 'IP address / host', 'placeholder' => 'E.g. 192.168.1.50', 'helper_text' => 'Local network address (10.x, 172.16-31.x, 192.168.x) or a network name resolving there; other addresses only if listed in labeling.allowed_printer_hosts'],
        'port' => ['label' => 'Port', 'placeholder' => '9100', 'helper_text' => 'Raw printer port, usually 9100'],
        'is_active' => ['label' => 'Active', 'placeholder' => '', 'helper_text' => ''],
    ],
];
