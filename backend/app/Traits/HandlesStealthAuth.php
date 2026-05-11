<?php

namespace App\Traits;

use App\Models\Group;
use Illuminate\Support\Facades\Gate;

trait HandlesStealthAuth
{
    protected function authorizeStealth(Group $group, string $ability, ?string $message = null, mixed $target = null): void
    {
        $policySubject = $target ?? $group;

        if (Gate::denies($ability, $policySubject)) {
            if ($group->parent_id && $group->is_private_child) {
                abort(404, __('Group not found.'));
            }
            abort(403, $message ?? __('This action is unauthorized'));
        }
    }
}
