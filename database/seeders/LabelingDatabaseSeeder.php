<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Orchestratore Labeling. Identificatori e stampe non si seminano: dipendono dai modelli
 * Labelable dei moduli consumer.
 */
class LabelingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LabelTemplateSeeder::class,
            PrinterSeeder::class,
        ]);
    }
}
