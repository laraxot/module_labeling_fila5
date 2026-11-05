<?php

declare(strict_types=1);

return [
    'fields' => [
        'type' => ['label' => 'Tipo'],
        'value' => ['label' => 'Valore'],
        'identifiable_type' => ['label' => 'Tipo di elemento'],
        'identifiable_id' => ['label' => 'Id elemento'],
        'is_active' => ['label' => 'Attivo'],
        'assigned_at' => ['label' => 'Assegnato il'],
        'revoked_at' => ['label' => 'Revocato il'],
    ],
    'actions' => [
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri l\'identificatore'],
        'revoke' => [
            'label' => 'Revoca',
            'icon' => 'heroicon-o-no-symbol',
            'tooltip' => 'Disattiva l\'identificatore',
            'modal_heading' => 'Revocare l\'identificatore?',
            'modal_description' => 'L\'etichetta o il tag non identificheranno piu\' questo elemento.',
            'success' => 'Identificatore revocato',
        ],
        'layout' => ['label' => 'Vista', 'tooltip' => 'Cambia vista elenco/griglia'],
    ],
];
