<?php

namespace App\Policies;

use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroupMemberPolicy
{
    /**
     * Determine whether the user can view all members.
     */
    public function viewAny(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::GROUP_VIEW);
    }

    /**
     * Determine whether the user can view the group member.
     */
    public function view(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::GROUP_VIEW);
    }

    /**
     * Determine whether the user can invite group members.
     */
    public function invite(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_INVITE);
    }

    /**
     * Determine whether the user can kick the group members.
     */
    public function kick(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_KICK);
    }

    /**
     * Determine whether the user can sync member's roles.
     */
    public function syncRole(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_MANAGE_ROLES);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return false;
    }
}
