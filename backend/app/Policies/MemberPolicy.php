<?php

namespace App\Policies;

use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Group $group): bool
    {
        if ($user->hasGroupPermission($group, RolePermissions::GROUP_VIEW)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): bool
    {
        if ($user->hasGroupPermission($group, RolePermissions::GROUP_VIEW)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function invite(User $user, Group $group): bool
    {
        if ($user->hasGroupPermission($group, RolePermissions::MEMBER_INVITE)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function kick(User $user, Group $group): bool
    {
        if ($user->hasGroupPermission($group, RolePermissions::MEMBER_KICK)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return false;
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
