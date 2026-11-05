<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Labeling\Enums\PrinterDriverEnum;
use Modules\Labeling\Models\Printer;

/**
 * @extends Factory<Printer>
 */
class PrinterFactory extends Factory
{
    /** @var class-string<Printer> */
    protected $model = Printer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'PRN-'.$this->faker->unique()->numerify('####'),
            'name' => 'Zebra '.$this->faker->bothify('ZT###'),
            'driver' => PrinterDriverEnum::ZPL_TCP,
            'host' => $this->faker->localIpv4(),
            'port' => 9100,
            'dpi' => 203,
            'is_active' => true,
        ];
    }

    public function pdf(): static
    {
        return $this->state(fn (array $_attributes): array => [
            'driver' => PrinterDriverEnum::PDF_DOWNLOAD,
            'host' => null,
        ]);
    }

    public function at(string $host, int $port): static
    {
        return $this->state(fn (array $_attributes): array => ['host' => $host, 'port' => $port]);
    }
}
