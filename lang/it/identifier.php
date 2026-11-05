<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Identificatori',
        'plural' => 'Identificatori',
        'group' => ['name' => 'Etichettatura', 'description' => 'Etichette, identificatori e stampanti'],
        'label' => 'Identificatori',
        'icon' => 'heroicon-o-qr-code',
        'sort' => 30,
    ],
    'label' => 'Identificatore',
    'plural_label' => 'Identificatori',
    'plural' => ['label' => 'Identificatori'],
    'fields' => [
        'type' => ['label' => 'Tipo'],
        'value' => ['label' => 'Valore'],
    ],
    'actions' => [
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri l\'identificatore'],
        'back' => ['label' => 'Indietro', 'tooltip' => 'Torna all\'elenco'],
        'revoke' => [
            'label' => 'Revoca',
            'icon' => 'heroicon-o-no-symbol',
            'tooltip' => 'Disattiva l\'identificatore: il valore potra\' essere riassegnato',
            'modal_heading' => 'Revocare l\'identificatore?',
            'modal_description' => 'L\'etichetta o il tag non identificheranno piu\' questo elemento.',
            'success' => 'Identificatore revocato',
        ],
    ],
];
