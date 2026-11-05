<?php

declare(strict_types=1);

return [
    'values' => [
        'queued' => ['label' => 'In coda', 'color' => 'gray', 'icon' => 'heroicon-o-clock', 'description' => 'Etichetta preparata, invio non ancora tentato'],
        'sent' => ['label' => 'Inviata', 'color' => 'success', 'icon' => 'heroicon-o-check-circle', 'description' => 'Inviata alla stampante'],
        'rendered' => ['label' => 'Generata', 'color' => 'info', 'icon' => 'heroicon-o-document-check', 'description' => 'Generata ma non inviata (PDF o nessuna stampante)'],
        'failed' => ['label' => 'Fallita', 'color' => 'danger', 'icon' => 'heroicon-o-x-circle', 'description' => 'Invio fallito: vedi il campo errore'],
    ],
];
