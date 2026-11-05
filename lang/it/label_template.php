<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Template etichette',
        'plural' => 'Template etichette',
        'group' => ['name' => 'Etichettatura', 'description' => 'Etichette, identificatori e stampanti'],
        'label' => 'Template etichette',
        'icon' => 'heroicon-o-document-text',
        'sort' => 10,
    ],
    'label' => 'Template etichetta',
    'plural_label' => 'Template etichette',
    'plural' => ['label' => 'Template etichette'],
    'fields' => [
        'code' => ['label' => 'Codice'],
        'name' => ['label' => 'Nome'],
        'format' => ['label' => 'Formato'],
    ],
    'actions' => [
        'create' => ['label' => 'Nuovo template', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Crea un template di etichetta'],
        'edit' => ['label' => 'Modifica', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Modifica il template'],
        'delete' => ['label' => 'Elimina', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Elimina il template'],
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri il template'],
        'back' => ['label' => 'Indietro', 'tooltip' => 'Torna all\'elenco'],
    ],
];
