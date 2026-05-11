<?php

namespace App\Policies;

use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\User;

class GroupRolePolicy
{
    // Determine whether the user can display all the roles at the same time.
    public function viewAny(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES) || $user->hasGroupPermission($group, RolePermissions::ROLES_MANAGE);
    }

    // Determine whether the user can view individual roles in detail.
    public function view(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES) || $user->hasGroupPermission($group, RolePermissions::ROLES_MANAGE);
    }

    // Determine whether the user can create new roles.
    public function create(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

    // Determine whether the user can update existing roles.
    public function update(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

    // Determine whether the user can delete existing roles.
    public function delete(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

}
