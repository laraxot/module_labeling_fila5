<?php

declare(strict_types=1);

namespace Modules\Labeling\Providers;

use Modules\Xot\Providers\XotBaseEventServiceProvider;

/**
 * Registrato da XotBaseServiceProvider::register(): deve esistere anche senza listener.
 */
class EventServiceProvider extends XotBaseEventServiceProvider
{
    public string $name = 'Labeling';

    /** @var array<string, array<int, string>> */
    protected $listen = [];
}
