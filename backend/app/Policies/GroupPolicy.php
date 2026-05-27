<?php

namespace App\Policies;

use App\Enum\GroupType;
use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\User;

/**
 * GroupPolicy
 * 
 * Manages authorization for Groups. (Organizations, Teams and Individual Workspaces)
 * Implements hierarchical access where Parent Organization's permissions may cascadedown to child Teams.
 */
class GroupPolicy
{

    /**
     * Determine if the group is visible to the requester (User or Guest).
     * 
     * This method implements "Contextual Visibility":
     * 
     * 1. Private Teams & Individual Workspaces:
     *    - Requires an authenticated user.
     *    - Requires the "GROUP_VIEW" permission.
     * 
     * 2. Public Organizations and Non-private Teams:
     *    - Open access
     * 
     * @param \App\Models\User|null $user The authenticated user, or null (Guest).
     * @param \App\Models\Group $group The Group being requested.    
     */
    public function view(?User $user,  Group $group): bool
    {
        // If the group if flagged as private or is a personal workspace
        if ($user && ($group->is_private_child || $group->type === GroupType::INDIVIDUAL)) {
            return $user->hasGroupPermission($group, RolePermissions::GROUP_VIEW);
        } 

        // Default 'true' for public Organizations and Teams.
        return true;
    }

    /**
     * Determine if the user requesting the update has 'GROUP_UPDATE' permission.
     * 
     * @param \App\Models\User $user The user requesting the change.
     * @param \App\Models\Group $group The group being updated.
     */
    public function update(User $user,  Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::GROUP_UPDATE);
    }

    /**
     * Determine if the user is the group's owner.
     * 
     * @param \App\Models\User $user The user requesting the deletion.
     * @param \App\Models\Group $group The group being deleted.
     */
    public function delete(User $user,  Group $group): bool
    {
        return $group->owner_id === $user->id;
    }

    /**
     * Determine if the user has 'GROUP_CREATE_CHILD' permission.
     * 
     * @param \App\Models\User $user The user creating a child.
     * @param \App\Models\Group $group The group the child is created for.
     */
    public function createTeam(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::GROUP_CREATE_CHILD);
    }

    /**
     * Determine if the user is able to invite members.
     * 
     * @param \App\Models\User $user
     * @param \App\Models\Group $group
     */
    public function invite(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::MEMBER_INVITE);
    }

    public function viewLogs(User $user, Group $group): bool
    {
        return $user->hasGroupPermission($group, RolePermissions::AUDIT_LOG_VIEW);
    }
}
