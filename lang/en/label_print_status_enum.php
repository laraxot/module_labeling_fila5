<?php

declare(strict_types=1);

return [
    'values' => [
        'queued' => ['label' => 'Queued', 'color' => 'gray', 'icon' => 'heroicon-o-clock', 'description' => 'Label prepared, delivery not attempted yet'],
        'sent' => ['label' => 'Sent', 'color' => 'success', 'icon' => 'heroicon-o-check-circle', 'description' => 'Sent to the printer'],
        'rendered' => ['label' => 'Rendered', 'color' => 'info', 'icon' => 'heroicon-o-document-check', 'description' => 'PDF file generated, ready to download'],
        'failed' => ['label' => 'Failed', 'color' => 'danger', 'icon' => 'heroicon-o-x-circle', 'description' => 'Delivery failed: see the error field'],
    ],
];
