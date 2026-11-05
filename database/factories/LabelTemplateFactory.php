<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Labeling\Database\Seeders\LabelTemplateSeeder;
use Modules\Labeling\Enums\LabelFormatEnum;
use Modules\Labeling\Models\LabelTemplate;

/**
 * @extends Factory<LabelTemplate>
 */
class LabelTemplateFactory extends Factory
{
    /** @var class-string<LabelTemplate> */
    protected $model = LabelTemplate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'TPL-'.$this->faker->unique()->numerify('######'),
            'name' => $this->faker->words(3, true),
            'format' => LabelFormatEnum::ZPL,
            'width_mm' => 100,
            'height_mm' => 50,
            'dpi' => 203,
            'content' => LabelTemplateSeeder::ZPL_DEFAULT,
            'labelable_type' => null,
            'is_default' => false,
            'is_active' => true,
        ];
    }

    public function pdf(): static
    {
        return $this->state(fn (array $_attributes): array => [
            'format' => LabelFormatEnum::PDF,
            'content' => LabelTemplateSeeder::PDF_DEFAULT,
        ]);
    }

    /**
     * @param  class-string  $labelableType
     */
    public function forType(string $labelableType): static
    {
        return $this->state(fn (array $_attributes): array => [
            'labelable_type' => $labelableType,
            'is_default' => true,
        ]);
    }

    public function withContent(string $content): static
    {
        return $this->state(fn (array $_attributes): array => ['content' => $content]);
    }
}
