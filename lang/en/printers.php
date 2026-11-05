<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Code'],
        'name' => ['label' => 'Name'],
        'driver' => ['label' => 'Connection'],
        'host' => ['label' => 'Host'],
        'port' => ['label' => 'Port'],
        'dpi' => ['label' => 'Dpi'],
        'is_active' => ['label' => 'Active'],
    ],
    'actions' => [
        'create' => ['label' => 'New printer', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Register a printer'],
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the printer'],
        'edit' => ['label' => 'Edit', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Edit the printer'],
        'delete' => ['label' => 'Delete', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Delete the selected printers'],
        'layout' => ['label' => 'View', 'tooltip' => 'Switch list/grid view'],
    ],
];
