<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Modules\Labeling\Models\Identifier;
use Modules\Xot\Database\Migrations\XotBaseMigration;

/*
 * `identifiable_*` e' una FK cross-modulo (StockUnit, Location, ...): colonne indicizzate senza vincolo.
 * `active_value` = "{type}:{value}" solo finche' attivo (null dopo la revoca): l'indice unique rende
 * impossibile a livello DB lo stesso valore attivo due volte per lo stesso tipo.
 */
return new class extends XotBaseMigration
{
    protected ?string $model_class = Identifier::class;

    public function up(): void
    {
        $this->tableCreate(static function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->morphs('identifiable');
            $table->string('type', 32);
            $table->string('value');
            $table->string('active_value')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->index(['type', 'value', 'is_active']);
        });

        $this->tableUpdate(function (Blueprint $table): void {
            if (! $this->hasColumn('active_value')) {
                $table->string('active_value')->nullable()->unique();
            }

            $this->updateTimestamps($table, false);
        });
    }
};
