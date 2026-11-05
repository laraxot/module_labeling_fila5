<?php

declare(strict_types=1);

namespace Modules\Labeling\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;

/**
 * Lo storico stampe non si modifica a mano: `create` copre stampa e ristampa.
 */
class LabelPrintPolicy extends UserBasePolicy
{
    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_print.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_print.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_print.create');
    }

    public function update(UserContract $user): bool
    {
        return false;
    }

    public function delete(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_print.delete');
    }
}
