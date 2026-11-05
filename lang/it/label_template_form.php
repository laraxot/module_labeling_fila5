<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Codice', 'placeholder' => 'Es. SU-ZPL-100X50', 'helper_text' => 'Identificativo univoco del template'],
        'name' => ['label' => 'Nome', 'placeholder' => 'Es. Unita\' di stoccaggio 100x50', 'helper_text' => ''],
        'format' => ['label' => 'Formato', 'placeholder' => 'ZPL o PDF', 'helper_text' => 'ZPL per stampanti termiche Zebra, PDF per stampa da ufficio'],
        'labelable_type' => ['label' => 'Tipo di elemento', 'placeholder' => 'Tutti', 'helper_text' => 'Vuoto: template generico. I tipi disponibili sono registrati dai moduli (config labeling.labelables)'],
        'width_mm' => ['label' => 'Larghezza (mm)', 'placeholder' => '100', 'helper_text' => ''],
        'height_mm' => ['label' => 'Altezza (mm)', 'placeholder' => '50', 'helper_text' => ''],
        'dpi' => ['label' => 'Risoluzione (dpi)', 'placeholder' => '203', 'helper_text' => 'Risoluzione della testina: 203 dpi = 8 punti/mm'],
        'is_default' => ['label' => 'Predefinito', 'placeholder' => '', 'helper_text' => 'Usato quando la stampa non indica un template'],
        'is_active' => ['label' => 'Attivo', 'placeholder' => '', 'helper_text' => ''],
        'content' => [
            'label' => 'Contenuto',
            'placeholder' => '^XA ... ^XZ',
            'helper_text' => 'Testo ZPL o HTML con segnaposto {{ chiave }}, senza istruzioni. Chiavi: {{ code }}, {{ url }} (contenuto del QR), {{ width_mm }}, {{ height_mm }}, {{ dpi }}, {{ width_dots }}, {{ height_dots }}, {{ data.nome_campo }} (un dato dell\'elemento), {{ line_1 }}...{{ line_N }} (i dati in ordine); solo nei PDF {{ qr_svg }} (immagine del QR). Nei PDF niente immagini, link o file esterni.',
        ],
    ],
];
