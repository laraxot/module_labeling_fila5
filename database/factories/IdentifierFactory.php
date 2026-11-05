<?php

declare(strict_types=1);

namespace Modules\Labeling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Modules\Labeling\Enums\IdentifierTypeEnum;
use Modules\Labeling\Models\Identifier;

/**
 * Di default l'identificatore punta a un tipo fittizio: nei test si usa `forModel()`.
 *
 * @extends Factory<Identifier>
 */
class IdentifierFactory extends Factory
{
    /** @var class-string<Identifier> */
    protected $model = Identifier::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $value = 'CODE-'.$this->faker->unique()->numerify('########');

        return [
            'identifiable_type' => 'labelable',
            'identifiable_id' => $this->faker->numberBetween(1, 1_000_000),
            'type' => IdentifierTypeEnum::QR,
            'value' => $value,
            'active_value' => Identifier::activeValueFor(IdentifierTypeEnum::QR, $value),
            'is_active' => true,
            'assigned_at' => now()->toImmutable(),
            'revoked_at' => null,
        ];
    }

    public function forModel(Model $model): static
    {
        return $this->state(fn (array $_attributes): array => [
            'identifiable_type' => $model->getMorphClass(),
            'identifiable_id' => $model->getKey(),
        ]);
    }

    public function rfid(string $epc): static
    {
        return $this->state(fn (array $_attributes): array => [
            'type' => IdentifierTypeEnum::RFID_UHF,
            'value' => strtoupper($epc),
            'active_value' => Identifier::activeValueFor(IdentifierTypeEnum::RFID_UHF, strtoupper($epc)),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $_attributes): array => [
            'is_active' => false,
            'active_value' => null,
            'revoked_at' => now()->toImmutable(),
        ]);
    }
}
