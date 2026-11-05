<?php

declare(strict_types=1);

return [
    'fields' => [
        'id' => ['label' => 'No.'],
        'labelable_type' => ['label' => 'Item type'],
        'labelable_id' => ['label' => 'Item id'],
        'labelTemplate' => ['code' => ['label' => 'Template']],
        'printer' => ['code' => ['label' => 'Printer']],
        'label_template_id' => ['label' => 'Template'],
        'printer_id' => ['label' => 'Printer'],
        'copies' => ['label' => 'Copies'],
        'status' => ['label' => 'Outcome'],
        'printed_at' => ['label' => 'Processed at'],
        'error' => ['label' => 'Error'],
        'created_at' => ['label' => 'Requested at'],
    ],
    'actions' => [
        'view' => ['label' => 'Details', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Open the print'],
        'download' => ['label' => 'Download PDF', 'icon' => 'heroicon-o-arrow-down-tray', 'tooltip' => 'Download the generated PDF'],
        'reprint' => [
            'label' => 'Reprint',
            'icon' => 'heroicon-o-printer',
            'tooltip' => 'Reprint with the same template and printer',
            'modal_heading' => 'Reprint the label?',
            'modal_description' => 'A new print is created with the item\'s current data.',
        ],
        'layout' => ['label' => 'View', 'tooltip' => 'Switch list/grid view'],
    ],
];
