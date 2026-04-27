<?php

namespace App\Policies;

use App\Enum\GroupType;
use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\User;
use App\Traits\HasGroupPermissions;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user,  Group $group): bool
    {
        if ($user && ($group->is_private_child || $group->type === GroupType::INDIVIDUAL)) {
            return $user->hasGroupPermission($group, RolePermissions::GROUP_VIEW);
        } 

        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user,  Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::GROUP_UPDATE);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user,  Group $group): bool
    {
        return $group->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user,  Group $group): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user,  Group $group): bool
    {
        return false;
    }

    public function createTeam(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::GROUP_CREATE_CHILD);
    }

    public function invite(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_INVITE);
    }
}
