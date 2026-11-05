<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Stampanti',
        'plural' => 'Stampanti',
        'group' => ['name' => 'Etichettatura', 'description' => 'Etichette, identificatori e stampanti'],
        'label' => 'Stampanti',
        'icon' => 'heroicon-o-printer',
        'sort' => 20,
    ],
    'label' => 'Stampante',
    'plural_label' => 'Stampanti',
    'plural' => ['label' => 'Stampanti'],
    'fields' => [
        'code' => ['label' => 'Codice'],
        'name' => ['label' => 'Nome'],
    ],
    'actions' => [
        'create' => ['label' => 'Nuova stampante', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Censisci una stampante'],
        'edit' => ['label' => 'Modifica', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Modifica la stampante'],
        'delete' => ['label' => 'Elimina', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Elimina la stampante'],
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri la stampante'],
        'back' => ['label' => 'Indietro', 'tooltip' => 'Torna all\'elenco'],
    ],
];
