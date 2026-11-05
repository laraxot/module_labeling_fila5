<?php

declare(strict_types=1);

return [
    'fields' => [
        'id' => ['label' => 'N.'],
        'labelable_type' => ['label' => 'Tipo di elemento'],
        'labelable_id' => ['label' => 'Id elemento'],
        'labelTemplate' => ['code' => ['label' => 'Template']],
        'printer' => ['code' => ['label' => 'Stampante']],
        'label_template_id' => ['label' => 'Template'],
        'printer_id' => ['label' => 'Stampante'],
        'copies' => ['label' => 'Copie'],
        'status' => ['label' => 'Esito'],
        'printed_at' => ['label' => 'Elaborata il'],
        'error' => ['label' => 'Errore'],
        'created_at' => ['label' => 'Richiesta il'],
    ],
    'actions' => [
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri la stampa'],
        'download' => ['label' => 'Scarica PDF', 'icon' => 'heroicon-o-arrow-down-tray', 'tooltip' => 'Scarica il PDF generato'],
        'reprint' => [
            'label' => 'Ristampa',
            'icon' => 'heroicon-o-printer',
            'tooltip' => 'Ristampa con lo stesso template e la stessa stampante',
            'modal_heading' => 'Ristampare l\'etichetta?',
            'modal_description' => 'Viene creata una nuova stampa con i dati attuali dell\'elemento.',
        ],
        'layout' => ['label' => 'Vista', 'tooltip' => 'Cambia vista elenco/griglia'],
    ],
];
