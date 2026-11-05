<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Labeling\Models\Printer;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = Printer::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('code', 64)->unique();
            $table->string('name');
            $table->string('driver', 32);
            $table->string('host')->nullable();
            $table->unsignedInteger('port')->default(9100);
            $table->unsignedInteger('dpi')->default(203);
            $table->boolean('is_active')->default(true);
        });

        $this->tableUpdate(function (Blueprint $table): void {
            $this->updateTimestamps($table, true);
        });
    }
};
