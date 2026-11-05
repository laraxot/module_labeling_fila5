<?php

declare(strict_types=1);

return [
    'printer_unreachable' => 'Printer :printer is unreachable at :host::port.',
    'printer_address' => [
        'missing_host' => 'Enter the printer IP address or network name.',
        'invalid_host' => ':host is not a valid IP address or network name.',
        'unresolvable' => 'Network name :host does not resolve to an IPv4 address.',
        'not_allowed' => 'Address :host is not allowed for a printer: only local network addresses or those listed in labeling.allowed_printer_hosts are allowed.',
        'port' => 'Port :port is not allowed: use a port between :min and :max.',
    ],
    'printer_missing_host' => 'Printer :printer has no network address.',
    'printer_write_failed' => 'Sending to printer :printer was interrupted.',
    'printer_timeout' => 'Printer :printer did not accept the data within :seconds seconds.',
    'printer_not_configured' => 'No active printer for template :template (:format): pick a printer or configure :key. Label not printed.',
    'printer_inactive' =>'Printer :printer is not active.',
    'incompatible_printer' => 'Printer :printer (:driver) cannot print template :template in :format format.',
    'template_not_found' => 'No active label template for :type.',
    'template_inactive' => 'Template :template is not active.',
    'render_failed' => 'Error in template :template: :reason',
    'template_content' => [
        'php_tag' => 'The template cannot contain PHP code (<?).',
        'raw_echo' => 'The template cannot contain {!! !!}: use {{ key }} placeholders.',
        'directive' => 'The template cannot contain instructions such as :directive: plain text and {{ key }} placeholders only.',
        'unknown_placeholder' => 'Unknown placeholder: :placeholder.',
        'external_resource' => 'A PDF template cannot load files or addresses (:resource): the only image allowed is {{ qr_svg }}.',
    ],
    'qr_failed' => 'Unable to generate the QR code: :reason',
    'pdf_failed' => 'Unable to generate the label PDF: :reason',
    'identifier_already_assigned' => 'Value :value (:type) is already assigned to another item.',
    'identifier_value_required' => 'A :type identifier needs the value read from the tag.',
    'labelable_not_saved' => 'Save the item before assigning it an identifier.',
];
