<?php

declare(strict_types=1);

namespace Modules\Labeling\Providers;

use Modules\Xot\Providers\XotBaseServiceProvider;
use Override;

/**
 * Service Provider del modulo Labeling.
 *
 * Le proprieta' sono `$module_dir` / `$module_ns` (snake case) perche' sono quelle lette da
 * XotBaseServiceProvider::boot()/register(): con i nomi camelCase la base userebbe la propria
 * directory e registrerebbe i provider di Xot al posto di quelli del modulo.
 */
class LabelingServiceProvider extends XotBaseServiceProvider
{
    public string $name = 'Labeling';

    protected string $module_dir = __DIR__;

    protected string $module_ns = __NAMESPACE__;

    /**
     * Config del modulo sotto la chiave `labeling` (non `labeling.config`), come Activity:
     * i consumer leggono e scrivono `config('labeling.labelables')`.
     */
    #[Override]
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->name, 'config/config.php') => config_path('labeling.php'),
        ], 'config');

        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), 'labeling');
    }
}
