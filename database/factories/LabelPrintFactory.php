<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Modules\Labeling\Enums\LabelPrintStatusEnum;
use Modules\Labeling\Models\LabelPrint;
use Modules\Labeling\Models\LabelTemplate;

/**
 * @extends Factory<LabelPrint>
 */
class LabelPrintFactory extends Factory
{
    /** @var class-string<LabelPrint> */
    protected $model = LabelPrint::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'labelable_type' => 'labelable',
            'labelable_id' => $this->faker->numberBetween(1, 1_000_000),
            'label_template_id' => LabelTemplate::factory(),
            'printer_id' => null,
            'copies' => 1,
            'rendered' => '^XA^FDTEST^FS^XZ',
            'status' => LabelPrintStatusEnum::RENDERED,
            'error' => null,
            'output_path' => null,
            'printed_at' => now()->toImmutable(),
        ];
    }

    public function forModel(Model $model): static
    {
        return $this->state(fn (array $_attributes): array => [
            'labelable_type' => $model->getMorphClass(),
            'labelable_id' => $model->getKey(),
        ]);
    }

    public function failed(string $error = 'Connection refused'): static
    {
        return $this->state(fn (array $_attributes): array => [
            'status' => LabelPrintStatusEnum::FAILED,
            'error' => $error,
            'printed_at' => null,
        ]);
    }
}
