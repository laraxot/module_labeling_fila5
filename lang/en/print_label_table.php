<?php

declare(strict_types=1);

return [
    'fields' => [
        'label_template_id' => ['label' => 'Template', 'placeholder' => 'Choose a template', 'helper_text' => 'The default template for this kind of item is preselected'],
        'printer_id' => ['label' => 'Printer', 'placeholder' => 'Default printer', 'helper_text' => 'Empty: default printer for the format; PDF templates produce a file'],
        'copies' => ['label' => 'Copies', 'placeholder' => '1', 'helper_text' => ''],
    ],
    'actions' => [
        'print_label' => [
            'label' => 'Print label',
            'tooltip' => 'Print the QR code label',
            'modal_heading' => 'Print label',
            'modal_description' => 'Choose template, printer and number of copies.',
            'submit' => 'Print',
            'success' => 'Label processed',
            'error' => 'Printing failed',
        ],
    ],
];
