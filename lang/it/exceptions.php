<?php

declare(strict_types=1);

return [
    'printer_unreachable' => 'Stampante :printer non raggiungibile su :host::port (:reason).',
    'printer_missing_host' => 'La stampante :printer non ha un indirizzo di rete configurato.',
    'printer_write_failed' => 'Invio alla stampante :printer interrotto.',
    'printer_timeout' => 'La stampante :printer non ha accettato i dati entro :seconds secondi.',
    'printer_inactive' => 'La stampante :printer non e\' attiva.',
    'incompatible_printer' => 'La stampante :printer (:driver) non puo\' stampare il template :template in formato :format.',
    'template_not_found' => 'Nessun template di etichetta attivo per :type.',
    'template_inactive' => 'Il template :template non e\' attivo.',
    'render_failed' => 'Errore nel template :template: :reason',
    'qr_failed' => 'Impossibile generare il QR code: :reason',
    'pdf_failed' => 'Impossibile generare il PDF dell\'etichetta: :reason',
    'identifier_already_assigned' => 'Il valore :value (:type) e\' gia\' assegnato a un altro elemento.',
    'identifier_value_required' => 'Per un identificatore :type serve il valore letto dal tag.',
    'labelable_not_saved' => 'L\'elemento va salvato prima di assegnargli un identificatore.',
];
