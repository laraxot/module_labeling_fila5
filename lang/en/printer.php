<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Printers',
        'plural' => 'Printers',
        'group' => ['name' => 'Labeling', 'description' => 'Labels, identifiers and printers'],
        'label' => 'Printers',
        'icon' => 'heroicon-o-printer',
        'sort' => 20,
    ],
    'label' => 'Printer',
    'plural_label' => 'Printers',
    'plural' => ['label' => 'Printers'],
    'fields' => [
        'code' => ['label' => 'Code'],
        'name' => ['label' => 'Name'],
    ],
    'actions' => [
        'create' => ['label' => 'New printer', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Register a printer'],
        'edit' => ['label' => 'Edit', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Edit the printer'],
        'delete' => ['label' => 'Delete', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Delete the printer'],
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the printer'],
        'back' => ['label' => 'Back', 'tooltip' => 'Back to the list'],
    ],
];
