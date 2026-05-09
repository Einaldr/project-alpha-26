<?php

namespace App\Policies;

use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\User;

class GroupRolePolicy
{
    public function anyView(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES) || $user->hasGroupPermission($group, RolePermissions::ROLES_MANAGE);
    }

    public function view(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES) || $user->hasGroupPermission($group, RolePermissions::ROLES_MANAGE);
    }

    public function create(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

    public function update(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

    public function delete(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

}
