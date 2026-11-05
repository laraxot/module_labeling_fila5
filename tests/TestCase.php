<?php

declare(strict_types=1);

namespace Modules\Labeling\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\ServiceProvider;
use Modules\Labeling\Providers\LabelingServiceProvider;
use Modules\Xot\Tests\XotBaseTestCase;
use Modules\Xot\Tests\XotModuleSchema;

/**
 * Base dei test Labeling, come Activity: sqlite condiviso, nessun migrate:fresh, transazione per test.
 * Le tabelle del modulo si creano (solo se mancanti) con XotModuleSchema prima che parta la transazione.
 * Richiede la connessione `labeling` in config/database.php del progetto.
 */
abstract class TestCase extends XotBaseTestCase
{
    use DatabaseTransactions;

    /** @var list<string> */
    protected $connectionsToTransact = ['sqlite', 'labeling'];

    protected function setUp(): void
    {
        $this->prepareSharedSqliteForTesting();
        XotModuleSchema::ensure('Labeling');

        parent::setUp();
    }

    /**
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders(Application $app): array
    {
        return [
            ...parent::getPackageProviders($app),
            LabelingServiceProvider::class,
        ];
    }
}
