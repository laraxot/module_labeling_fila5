<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Codice'],
        'name' => ['label' => 'Nome'],
        'format' => ['label' => 'Formato'],
        'labelable_type' => ['label' => 'Tipo di elemento'],
        'width_mm' => ['label' => 'Larghezza (mm)'],
        'height_mm' => ['label' => 'Altezza (mm)'],
        'dpi' => ['label' => 'Dpi'],
        'is_default' => ['label' => 'Predefinito'],
        'is_active' => ['label' => 'Attivo'],
        'updated_at' => ['label' => 'Ultima modifica'],
    ],
    'actions' => [
        'create' => ['label' => 'Nuovo template', 'icon' => 'heroicon-o-plus', 'tooltip' => 'Crea un template'],
        'view' => ['label' => 'Dettaglio', 'icon' => 'heroicon-o-eye', 'tooltip' => 'Apri il template'],
        'edit' => ['label' => 'Modifica', 'icon' => 'heroicon-o-pencil-square', 'tooltip' => 'Modifica il template'],
        'delete' => ['label' => 'Elimina', 'icon' => 'heroicon-o-trash', 'tooltip' => 'Elimina i template selezionati'],
        'layout' => ['label' => 'Vista', 'tooltip' => 'Cambia vista elenco/griglia'],
    ],
];
