<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Modules\Labeling\Models\Identifier;
use Spatie\QueueableAction\QueueableAction;

/**
 * Disattiva un identificatore: libera il valore (`active_value = null`) e registra `revoked_at`.
 * Idempotente su un identificatore gia' revocato.
 */
class RevokeIdentifierAction
{
    use QueueableAction;

    public function execute(Identifier $identifier): Identifier
    {
        if (! $identifier->is_active) {
            return $identifier;
        }

        $identifier->is_active = false;
        $identifier->active_value = null;
        $identifier->revoked_at = now()->toImmutable();
        $identifier->save();

        return $identifier;
    }
}
