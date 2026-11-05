<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Codice'],
        'name' => ['label' => 'Nome'],
        'driver' => ['label' => 'Collegamento'],
        'host' => ['label' => 'Host'],
        'port' => ['label' => 'Porta'],
        'dpi' => ['label' => 'Dpi'],
        'is_active' => ['label' => 'Attiva'],
    ],
    'actions' => [
        'create' => ['label' => 'Nuova stampante', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Censisci una stampante'],
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri la stampante'],
        'edit' => ['label' => 'Modifica', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Modifica la stampante'],
        'delete' => ['label' => 'Elimina', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Elimina le stampanti selezionate'],
        'layout' => ['label' => 'Vista', 'tooltip' => 'Cambia vista elenco/griglia'],
    ],
];
