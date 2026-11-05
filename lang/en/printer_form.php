<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Code', 'placeholder' => 'E.g. ZEBRA-GATE', 'helper_text' => 'Also used in config labeling.default_printers'],
        'name' => ['label' => 'Name', 'placeholder' => 'E.g. Zebra ZT411 inbound scale', 'helper_text' => ''],
        'driver' => ['label' => 'Connection', 'placeholder' => '', 'helper_text' => ''],
        'dpi' => ['label' => 'Resolution (dpi)', 'placeholder' => '203', 'helper_text' => ''],
        'host' => ['label' => 'IP address / host', 'placeholder' => 'E.g. 192.168.1.50', 'helper_text' => 'The printer must be reachable from the server'],
        'port' => ['label' => 'Port', 'placeholder' => '9100', 'helper_text' => 'Raw printer port, usually 9100'],
        'is_active' => ['label' => 'Active', 'placeholder' => '', 'helper_text' => ''],
    ],
];
