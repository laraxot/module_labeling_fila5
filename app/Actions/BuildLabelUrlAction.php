<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Spatie\QueueableAction\QueueableAction;
use Webmozart\Assert\Assert;

/**
 * URL codificato nel QR: `url('/l/{code}')`, risolto dalla route `labeling.resolve`.
 * Funziona anche con la fotocamera del telefono, fuori dall'app.
 */
class BuildLabelUrlAction
{
    use QueueableAction;

    public function execute(string $code): string
    {
        Assert::stringNotEmpty($code);

        return url('/l/'.rawurlencode($code));
    }
}
