<?php

namespace App\Traits;

use App\Models\Group;
use Illuminate\Support\Facades\Gate;

trait HandlesStealthAuth
{
    protected function authrizeStealth(Group $group, string $ability, ?string $message = null): void
    {
        if (Gate::denies($ability, $group)) {
            if ($group->parent_id && $group->is_private_child) {
                abort(404, __('Group not found.'));
            }
            abort(403, $message ?? __('This action is unauthorized'));
        }
    }
}
