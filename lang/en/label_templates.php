<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Code'],
        'name' => ['label' => 'Name'],
        'format' => ['label' => 'Format'],
        'labelable_type' => ['label' => 'Item type'],
        'width_mm' => ['label' => 'Width (mm)'],
        'height_mm' => ['label' => 'Height (mm)'],
        'dpi' => ['label' => 'Dpi'],
        'is_default' => ['label' => 'Default'],
        'is_active' => ['label' => 'Active'],
        'updated_at' => ['label' => 'Last modified'],
    ],
    'actions' => [
        'create' => ['label' => 'New template', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Create a template'],
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the template'],
        'edit' => ['label' => 'Edit', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Edit the template'],
        'delete' => ['label' => 'Delete', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Delete the selected templates'],
        'layout' => ['label' => 'View', 'tooltip' => 'Switch list/grid view'],
    ],
];
