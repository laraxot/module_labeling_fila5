<?php

declare(strict_types=1);

return [
    'fields' => [
        'type' => ['label' => 'Type'],
        'value' => ['label' => 'Value'],
        'identifiable_type' => ['label' => 'Item type'],
        'identifiable_id' => ['label' => 'Item id'],
        'is_active' => ['label' => 'Active'],
        'assigned_at' => ['label' => 'Assigned at'],
        'revoked_at' => ['label' => 'Revoked at'],
    ],
    'actions' => [
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the identifier'],
        'revoke' => [
            'label' => 'Revoke',
            'icon' => 'heroicon-o-no-symbol',
            'tooltip' => 'Deactivate the identifier',
            'modal_heading' => 'Revoke the identifier?',
            'modal_description' => 'The label or tag will no longer identify this item.',
            'success' => 'Identifier revoked',
        ],
        'layout' => ['label' => 'View', 'tooltip' => 'Switch list/grid view'],
    ],
];
