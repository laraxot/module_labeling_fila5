<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Labeling\Enums\PrinterDriverEnum;
use Modules\Labeling\Models\Printer;

/**
 * Una "stampante" PDF sempre disponibile. Le stampanti ZPL di rete si censiscono dalla UI
 * (host e porta dipendono dall'impianto).
 */
class PrinterSeeder extends Seeder
{
    public function run(): void
    {
        Printer::query()->updateOrCreate(['code' => 'PDF'], [
            'name' => 'PDF',
            'driver' => PrinterDriverEnum::PDF_DOWNLOAD,
            'host' => null,
            'port' => 9100,
            'dpi' => 203,
            'is_active' => true,
        ]);
    }
}
