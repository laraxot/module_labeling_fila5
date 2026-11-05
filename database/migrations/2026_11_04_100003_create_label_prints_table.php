<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Labeling\Models\LabelPrint;
use Modules\Xot\Database\Migrations\XotBaseMigration;

return new class extends XotBaseMigration
{
    protected ?string $model_class = LabelPrint::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->morphs('labelable');
            $table->unsignedBigInteger('label_template_id')->index();
            $table->unsignedBigInteger('printer_id')->nullable()->index();
            $table->unsignedInteger('copies')->default(1);
            $table->longText('rendered');
            $table->string('status', 16)->index();
            $table->text('error')->nullable();
            $table->string('output_path')->nullable();
            $table->timestamp('printed_at')->nullable();
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('output_path')) {
                $table->string('output_path')->nullable();
            }

            $this->updateTimestamps($table, false);
        });
    }
};
