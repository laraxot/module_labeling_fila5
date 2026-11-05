<?php

declare(strict_types=1);

namespace Modules\Labeling\Providers;

use Modules\Xot\Providers\XotBaseRouteServiceProvider;

class RouteServiceProvider extends XotBaseRouteServiceProvider
{
    public string $name = 'Labeling';

    protected string $moduleNamespace = 'Modules\Labeling\Http\Controllers';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;
}
