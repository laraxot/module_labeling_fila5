<?php

declare(strict_types=1);

namespace Modules\Labeling\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Models\Traits\HasIdentifiers;

/**
 * Modello Labelable minimo per i test: tabella creata al volo (dentro la transazione del test).
 *
 * @property int $id
 * @property string $code
 * @property string|null $name
 */
class LabelableItem extends Model implements LabelableContract
{
    use HasIdentifiers;

    public const string TABLE = 'labeling_test_items';

    /** @var string */
    protected $connection = 'labeling';

    /** @var string */
    protected $table = self::TABLE;

    /** @var list<string> */
    protected $fillable = ['code', 'name'];

    public static function ensureTable(): void
    {
        $schema = Schema::connection('labeling');
        if ($schema->hasTable(self::TABLE)) {
            return;
        }

        $schema->create(self::TABLE, static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public static function createWith(string $code, ?string $name = null): self
    {
        self::ensureTable();

        return self::query()->create(['code' => $code, 'name' => $name]);
    }

    public function getLabelCode(): string
    {
        return $this->code;
    }

    /** @return array<string, string|int|float|null> */
    public function getLabelData(): array
    {
        return [
            'name' => $this->name,
            'weight' => 12.5,
        ];
    }
}
