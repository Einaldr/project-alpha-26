<?php

namespace App\Traits;

use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\GroupMember;

trait HasGroupPermissions
{
    public function hasGroupPermission(Group $group, RolePermissions $permission): bool
    {
        if ($this->hasDirectPermissions($group, $permission)) {
            return true;
        }

        if ($group->parent_id) {
            $parent = $group->relationLoaded('parent') ? $group->parent : $group->parent()->first();

            return $parent ? $this->hasGroupPermission($parent, $permission) : false;
        }

        return false;
    }

    protected function hasDirectPermissions(Group $group, RolePermissions $permission): bool
    {
        if ($group->owner_id === $this->id) {
            return true;
        }

        return $this->memberships()
                    ->where('group_id', $group->id)
                    ->whereHas('roles', function ($query) use ($permission) {
                        $query->whereJsonContains('permissions', $permission->value);
                    })
                    ->exists();
   }
}
