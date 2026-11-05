<?php

declare(strict_types=1);

return [
    'values' => [
        'qr' => ['label' => 'QR code', 'color' => 'primary', 'icon' => 'heroicon-o-qr-code', 'description' => 'Printed QR code (holds the /l/{code} URL)'],
        'barcode128' => ['label' => 'Code 128', 'color' => 'gray', 'icon' => 'heroicon-o-bars-3', 'description' => 'Linear Code 128 barcode'],
        'rfid_uhf' => ['label' => 'UHF RFID', 'color' => 'info', 'icon' => 'heroicon-o-signal', 'description' => 'UHF RFID tag (hexadecimal EPC)'],
        'nfc' => ['label' => 'NFC', 'color' => 'success', 'icon' => 'heroicon-o-wifi', 'description' => 'NFC tag (hexadecimal UID)'],
    ],
];
