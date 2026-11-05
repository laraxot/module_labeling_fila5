<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Identifiers',
        'plural' => 'Identifiers',
        'group' => ['name' => 'Labeling', 'description' => 'Labels, identifiers and printers'],
        'label' => 'Identifiers',
        'icon' => 'heroicon-o-qr-code',
        'sort' => 30,
    ],
    'label' => 'Identifier',
    'plural_label' => 'Identifiers',
    'plural' => ['label' => 'Identifiers'],
    'fields' => [
        'type' => ['label' => 'Type'],
        'value' => ['label' => 'Value'],
    ],
    'actions' => [
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the identifier'],
        'back' => ['label' => 'Back', 'tooltip' => 'Back to the list'],
        'revoke' => [
            'label' => 'Revoke',
            'icon' => 'heroicon-o-no-symbol',
            'tooltip' => 'Deactivate the identifier: the value can be reassigned',
            'modal_heading' => 'Revoke the identifier?',
            'modal_description' => 'The label or tag will no longer identify this item.',
            'success' => 'Identifier revoked',
        ],
    ],
];
