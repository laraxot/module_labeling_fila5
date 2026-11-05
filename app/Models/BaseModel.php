<?php

declare(strict_types=1);

namespace Modules\Labeling\Models;

use Modules\Xot\Models\XotBaseModel;

/**
 * Base dei modelli Labeling: connessione `labeling`, chiavi bigIncrements (id intero).
 *
 * @see XotBaseModel
 */
abstract class BaseModel extends XotBaseModel
{
    /** @var string */
    protected $connection = 'labeling';

    /**
     * XotBaseModel castava `id` a string (retaggio uuid): qui le chiavi sono intere.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'id' => 'integer',
        ]);
    }
}
