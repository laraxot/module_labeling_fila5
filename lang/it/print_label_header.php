<?php

declare(strict_types=1);

/*
 * PrintLabelHeaderAction (header action riusata da altri moduli): etichetta, tooltip e notifiche
 * sono lette da `actions.print_label.*`, i campi del modale da `fields.*`.
 */
return [
    'fields' => [
        'label_template_id' => ['label' => 'Template', 'placeholder' => 'Scegli il template', 'helper_text' => 'Proposto il template di default per questo tipo di elemento'],
        'printer_id' => ['label' => 'Stampante', 'placeholder' => 'Stampante di default', 'helper_text' => 'Vuoto: stampante di default del formato; per i PDF viene generato il file'],
        'copies' => ['label' => 'Copie', 'placeholder' => '1', 'helper_text' => ''],
    ],
    'actions' => [
        'print_label' => [
            'label' => 'Stampa etichetta',
            'tooltip' => 'Stampa l\'etichetta con QR code',
            'modal_heading' => 'Stampa etichetta',
            'modal_description' => 'Scegli template, stampante e numero di copie.',
            'submit' => 'Stampa',
            'success' => 'Etichetta elaborata',
            'error' => 'Stampa non riuscita',
        ],
    ],
];
