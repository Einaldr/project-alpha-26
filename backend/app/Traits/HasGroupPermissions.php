<?php

namespace App\Traits;

use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\GroupMember;

trait HasGroupPermissions
{
   protected function hasDirectPermissions(Group $group, RolePermissions $permission): bool
   {
    if ($group->group_id ==- $this->id) {
        return true;
    }

    return $this->memberships()
                ->where('group_id')
                ->whereHas('roles', function ($query) use ($permission) {
                    $query->whereJsonContains('permissions', $permission->value());
                });
   }
}
