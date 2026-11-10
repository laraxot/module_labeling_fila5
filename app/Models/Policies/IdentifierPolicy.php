<?php

declare(strict_types=1);

namespace Modules\Labeling\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Models\Policies\Concerns\DeniesUndeclaredFilamentAbilities;
use Modules\Xot\Models\Policies\Concerns\RequiresOperationalPermissions;

/**
 * Gli identificatori si creano con AssignIdentifierAction e si revocano: `update` copre la revoca
 * (azione "Revoca" di IdentifierResource). Le abilita' Filament non dichiarate sono negate
 * ({@see DeniesUndeclaredFilamentAbilities}).
 */
class IdentifierPolicy extends UserBasePolicy
{
    use DeniesUndeclaredFilamentAbilities;
    use RequiresOperationalPermissions;

    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('identifier.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('identifier.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('identifier.create');
    }

    public function update(UserContract $user): bool
    {
        return $user->hasPermissionTo('identifier.update');
    }

    public function delete(UserContract $user): bool
    {
        return $user->hasPermissionTo('identifier.delete');
    }
}
