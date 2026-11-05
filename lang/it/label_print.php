<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Storico stampe',
        'plural' => 'Storico stampe',
        'group' => ['name' => 'Etichettatura', 'description' => 'Etichette, identificatori e stampanti'],
        'label' => 'Storico stampe',
        'icon' => 'heroicon-o-clock',
        'sort' => 40,
    ],
    'label' => 'Stampa etichetta',
    'plural_label' => 'Stampe etichette',
    'plural' => ['label' => 'Stampe etichette'],
    'fields' => [
        'status' => ['label' => 'Esito'],
    ],
    'actions' => [
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri la stampa'],
        'back' => ['label' => 'Indietro', 'tooltip' => 'Torna all\'elenco'],
        'download' => ['label' => 'Scarica PDF', 'icon' => 'heroicon-o-arrow-down-tray', 'tooltip' => 'Scarica il PDF generato'],
        'reprint' => [
            'label' => 'Ristampa',
            'icon' => 'heroicon-o-printer',
            'tooltip' => 'Ristampa con lo stesso template e la stessa stampante',
            'modal_heading' => 'Ristampare l\'etichetta?',
            'modal_description' => 'Viene creata una nuova stampa con i dati attuali dell\'elemento.',
        ],
    ],
];
