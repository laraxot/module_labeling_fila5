<?php

declare(strict_types=1);

namespace Modules\Labeling\Models\Policies;

use Modules\User\Models\Policies\UserBasePolicy;
use Modules\Xot\Contracts\UserContract;

class LabelTemplatePolicy extends UserBasePolicy
{
    public function viewAny(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_template.viewAny');
    }

    public function view(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_template.view');
    }

    public function create(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_template.create');
    }

    public function update(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_template.update');
    }

    public function delete(UserContract $user): bool
    {
        return $user->hasPermissionTo('label_template.delete');
    }
}
