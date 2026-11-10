<?php

declare(strict_types=1);

namespace Modules\Labeling\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;
use Modules\Xot\Models\Policies\Concerns\RequiresOperationalPermissions;

/**
 * Le abilita' Filament non dichiarate (eliminazione massiva, replica, riordino, ...) sono negate
 * ({@see DeniesUndeclaredFilamentAbilities}).
 */
class PrinterPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;
    use RequiresOperationalPermissions;

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('printer.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('printer.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('printer.create');
    }

    public function update(UserContract $user): bool
    {
        return $user->hasPermissionTo('printer.update');
    }

    public function delete(UserContract $user): bool
    {
        return $user->hasPermissionTo('printer.delete');
    }
}
