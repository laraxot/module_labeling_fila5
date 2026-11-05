<?php

declare(strict_types=1);

return [
    'values' => [
        'qr' => ['label' => 'QR code', 'color' => 'primary', 'icon' => 'heroicon-o-qr-code', 'description' => 'Codice QR stampato in etichetta (contiene l\'URL /l/{codice})'],
        'barcode128' => ['label' => 'Code 128', 'color' => 'gray', 'icon' => 'heroicon-o-bars-3', 'description' => 'Codice a barre lineare Code 128'],
        'rfid_uhf' => ['label' => 'RFID UHF', 'color' => 'info', 'icon' => 'heroicon-o-signal', 'description' => 'Tag RFID UHF (EPC esadecimale)'],
        'nfc' => ['label' => 'NFC', 'color' => 'success', 'icon' => 'heroicon-o-wifi', 'description' => 'Tag NFC (UID esadecimale)'],
    ],
];
