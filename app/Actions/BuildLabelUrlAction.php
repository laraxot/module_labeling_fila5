<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * URL codificato nel QR: `{APP_URL}/l/{code}`, risolto dalla route `labeling.resolve`.
 * Funziona anche con la fotocamera del telefono, fuori dall'app.
 *
 * La radice e' sempre `config('app.url')`, mai l'Host della richiesta in corso (`url()`): un'etichetta
 * stampata da una richiesta con Host manipolato porterebbe altrimenti un QR verso un dominio esterno
 * (audit di sicurezza B2). Le stampe in coda (senza richiesta) usano la stessa radice.
 */
class BuildLabelUrlAction
{
    use QueueableAction;

    public function execute(string $code): string
    {
        Assert::stringNotEmpty($code);

        $root = rtrim(config()->string('app.url'), '/');
        Assert::stringNotEmpty($root, 'config app.url (APP_URL) is required to build label URLs');

        return $root.'/l/'.rawurlencode($code);
    }
}
