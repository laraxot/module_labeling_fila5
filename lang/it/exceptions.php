<?php

declare(strict_types=1);

return [
    'printer_unreachable' => 'Stampante :printer non raggiungibile su :host::port.',
    'printer_address' => [
        'missing_host' => 'Indicare l\'indirizzo IP o il nome di rete della stampante.',
        'invalid_host' => ':host non e\' un indirizzo IP o un nome di rete valido.',
        'unresolvable' => 'Il nome di rete :host non si risolve in un indirizzo IPv4.',
        'not_allowed' => 'L\'indirizzo :host non e\' consentito per una stampante: sono ammessi solo indirizzi della rete locale o quelli elencati in labeling.allowed_printer_hosts.',
        'port' => 'Porta :port non consentita: usare una porta fra :min e :max.',
    ],
    'printer_missing_host' => 'La stampante :printer non ha un indirizzo di rete configurato.',
    'printer_write_failed' => 'Invio alla stampante :printer interrotto.',
    'printer_timeout' => 'La stampante :printer non ha accettato i dati entro :seconds secondi.',
    'printer_not_configured' => 'Nessuna stampante attiva per il template :template (:format): scegliere una stampante o configurare :key. Etichetta non stampata.',
    'printer_inactive' =>'La stampante :printer non e\' attiva.',
    'incompatible_printer' => 'La stampante :printer (:driver) non puo\' stampare il template :template in formato :format.',
    'template_not_found' => 'Nessun template di etichetta attivo per :type.',
    'template_inactive' => 'Il template :template non e\' attivo.',
    'render_failed' => 'Errore nel template :template: :reason',
    'template_content' => [
        'php_tag' => 'Il template non puo\' contenere codice PHP (<?).',
        'raw_echo' => 'Il template non puo\' contenere {!! !!}: usare i segnaposto {{ chiave }}.',
        'directive' => 'Il template non puo\' contenere istruzioni come :directive: solo testo e segnaposto {{ chiave }}.',
        'unknown_placeholder' => 'Segnaposto non riconosciuto: :placeholder.',
        'external_resource' => 'Il template PDF non puo\' caricare file o indirizzi (:resource): l\'unica immagine ammessa e\' {{ qr_svg }}.',
    ],
    'qr_failed' => 'Impossibile generare il QR code: :reason',
    'pdf_failed' => 'Impossibile generare il PDF dell\'etichetta: :reason',
    'identifier_already_assigned' => 'Il valore :value (:type) e\' gia\' assegnato a un altro elemento.',
    'identifier_value_required' => 'Per un identificatore :type serve il valore letto dal tag.',
    'labelable_not_saved' => 'L\'elemento va salvato prima di assegnargli un identificatore.',
];
