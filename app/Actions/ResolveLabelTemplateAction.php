<?php

declare(strict_types=1);

namespace Modules\Labeling\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Labeling\Contracts\LabelableContract;
use Modules\Labeling\Exceptions\LabelTemplateNotFoundException;
use Modules\Labeling\Models\LabelTemplate;
use Spatie\QueueableAction\QueueableAction;

/**
 * Template da usare quando la stampa non ne indica uno: il default attivo per la classe del modello,
 * altrimenti un template attivo della classe, altrimenti il default generico (`labelable_type` null).
 * A parita' vince il piu' recente.
 *
 * @throws LabelTemplateNotFoundException
 */
class ResolveLabelTemplateAction
{
    use QueueableAction;

    public function execute(Model&LabelableContract $model): LabelTemplate
    {
        $types = array_values(array_unique([$model->getMorphClass(), $model::class]));

        $template = LabelTemplate::query()
            ->where('is_active', true)
            ->whereIn('labelable_type', $types)
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->first();

        $template ??= LabelTemplate::query()
            ->where('is_active', true)
            ->where('is_default', true)
            ->where(static fn (Builder $query): Builder => $query->whereNull('labelable_type')->orWhere('labelable_type', ''))
            ->orderByDesc('id')
            ->first();

        if ($template === null) {
            throw LabelTemplateNotFoundException::forType($model::class);
        }

        return $template;
    }
}
