<?php

declare(strict_types=1);

return [
    'navigation' => [
        'name' => 'Label templates',
        'plural' => 'Label templates',
        'group' => ['name' => 'Labeling', 'description' => 'Labels, identifiers and printers'],
        'label' => 'Label templates',
        'icon' => 'heroicon-o-document-text',
        'sort' => 10,
    ],
    'label' => 'Label template',
    'plural_label' => 'Label templates',
    'plural' => ['label' => 'Label templates'],
    'fields' => [
        'code' => ['label' => 'Code'],
        'name' => ['label' => 'Name'],
        'format' => ['label' => 'Format'],
    ],
    'actions' => [
        'create' => ['label' => 'New template', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Create a label template'],
        'edit' => ['label' => 'Edit', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Edit the template'],
        'delete' => ['label' => 'Delete', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Delete the template'],
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the template'],
        'back' => ['label' => 'Back', 'tooltip' => 'Back to the list'],
    ],
];
