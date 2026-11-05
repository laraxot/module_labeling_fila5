<?php

declare(strict_types=1);

return [
    'printer_unreachable' => 'Printer :printer is unreachable at :host::port (:reason).',
    'printer_missing_host' => 'Printer :printer has no network address.',
    'printer_write_failed' => 'Sending to printer :printer was interrupted.',
    'printer_timeout' => 'Printer :printer did not accept the data within :seconds seconds.',
    'printer_inactive' => 'Printer :printer is not active.',
    'incompatible_printer' => 'Printer :printer (:driver) cannot print template :template in :format format.',
    'template_not_found' => 'No active label template for :type.',
    'template_inactive' => 'Template :template is not active.',
    'render_failed' => 'Error in template :template: :reason',
    'qr_failed' => 'Unable to generate the QR code: :reason',
    'pdf_failed' => 'Unable to generate the label PDF: :reason',
    'identifier_already_assigned' => 'Value :value (:type) is already assigned to another item.',
    'identifier_value_required' => 'A :type identifier needs the value read from the tag.',
    'labelable_not_saved' => 'Save the item before assigning it an identifier.',
];
