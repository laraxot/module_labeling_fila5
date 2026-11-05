<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Print history',
        'plural' => 'Print history',
        'group' => ['name' => 'Labeling', 'description' => 'Labels, identifiers and printers'],
        'label' => 'Print history',
        'icon' => 'heroicon-o-clock',
        'sort' => 40,
    ],
    'label' => 'Label print',
    'plural_label' => 'Label prints',
    'plural' => ['label' => 'Label prints'],
    'fields' => [
        'status' => ['label' => 'Outcome'],
    ],
    'actions' => [
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the print'],
        'back' => ['label' => 'Back', 'tooltip' => 'Back to the list'],
        'download' => ['label' => 'Download PDF', 'icon' => 'heroicon-o-arrow-down-tray', 'tooltip' => 'Download the generated PDF'],
        'reprint' => [
            'label' => 'Reprint',
            'icon' => 'heroicon-o-printer',
            'tooltip' => 'Reprint with the same template and printer',
            'modal_heading' => 'Reprint the label?',
            'modal_description' => 'A new print is created with the item\'s current data.',
        ],
    ],
];
