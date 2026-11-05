<?php

declare(strict_types=1);

return [
    'fields' => [
        'code' => ['label' => 'Code', 'placeholder' => 'E.g. SU-ZPL-100X50', 'helper_text' => 'Unique template identifier'],
        'name' => ['label' => 'Name', 'placeholder' => 'E.g. Stock unit 100x50', 'helper_text' => ''],
        'format' => ['label' => 'Format', 'placeholder' => 'ZPL or PDF', 'helper_text' => 'ZPL for Zebra thermal printers, PDF for office printing'],
        'labelable_type' => ['label' => 'Item type', 'placeholder' => 'All', 'helper_text' => 'Empty: generic template. Available types are registered by modules (config labeling.labelables)'],
        'width_mm' => ['label' => 'Width (mm)', 'placeholder' => '100', 'helper_text' => ''],
        'height_mm' => ['label' => 'Height (mm)', 'placeholder' => '50', 'helper_text' => ''],
        'dpi' => ['label' => 'Resolution (dpi)', 'placeholder' => '203', 'helper_text' => 'Print head resolution: 203 dpi = 8 dots/mm'],
        'is_default' => ['label' => 'Default', 'placeholder' => '', 'helper_text' => 'Used when a print does not specify a template'],
        'is_active' => ['label' => 'Active', 'placeholder' => '', 'helper_text' => ''],
        'content' => [
            'label' => 'Content',
            'placeholder' => '^XA ... ^XZ',
            'helper_text' => 'ZPL or HTML text with {{ key }} placeholders, no instructions. Keys: {{ code }}, {{ url }} (QR payload), {{ width_mm }}, {{ height_mm }}, {{ dpi }}, {{ width_dots }}, {{ height_dots }}, {{ data.field_name }} (one value of the item), {{ line_1 }}...{{ line_N }} (the values in order); PDF only {{ qr_svg }} (QR image). PDFs cannot use external images, links or files.',
        ],
    ],
];
