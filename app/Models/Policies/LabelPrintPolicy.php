<?php

declare(strict_types=1);

namespace Modules\Labeling\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;
use Modules\Xot\Models\Policies\Concerns\RequiresOperationalPermissions;

/**
 * Lo storico stampe non si modifica a mano. `create` copre stampa (PrintLabelTableAction /
 * PrintLabelHeaderAction, su qualunque modello Labelable) e ristampa; `view` copre il download del PDF.
 * Le abilita' Filament non dichiarate sono negate ({@see DeniesUndeclaredFilamentAbilities}).
 */
class LabelPrintPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;
    use RequiresOperationalPermissions;

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
