<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Labeling\Models\LabelTemplate;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = LabelTemplate::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('code', 64)->unique();
            $table->string('name');
            $table->string('format', 16);
            $table->unsignedInteger('width_mm');
            $table->unsignedInteger('height_mm');
            $table->unsignedInteger('dpi')->default(203);
            $table->longText('content');
            $table->string('labelable_type')->nullable()->index();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('labelable_type')) {
                $table->string('labelable_type')->nullable()->index();
            }

            $this->updateTimestamps($table, true);
        });
    }
};
