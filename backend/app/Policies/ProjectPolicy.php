<?php

namespace App\Policies;

use App\Enum\ProjectPermissions;
use App\Enum\RolePermissions;
use App\Models\Group;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine if the user can list projects inside a group.
     * 
     * Triggered by: [Project::class, $group]
     */
    public function viewAny(User $user, Group $group): bool
    {
        // 1. Group Owner always has access
        if ($group->owner_id === $user->id) {
            return true;
        }

        // 2. Members with GROUP_VIEW can list projects
        return $user->hasGroupPermission($group, RolePermissions::GROUP_VIEW);
    }

    /**
     * Determine if the user can view a specific project's files and details.
     * 
     * Triggered by: $project (Direct instance)
     */
    public function view(User $user, Project $project): bool
    {
        // 1. Group Owner Bypass
        if ($project->group->owner_id === $user->id) {
            return true;
        }

        // 2. Org Admins with PROJECT_MANAGE bypass
        if ($user->hasGroupPermission($project->group, RolePermissions::PROJECT_MANAGE)) {
            return true;
        }

        // 3. Explicit Project Members with VIEW permission
        $projectMember = $project->members()->where('user_id', $user->id)->first();

        if ($projectMember && in_array(ProjectPermissions::READ->value, $projectMember->permissions ?? [])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create a project in the group.
     * 
     * Triggered by: [Project::class, $group]
     */
    public function create(User $user, Group $group): bool
    {
        if ($group->owner_id === $user->id) {
            return true;
        }

        return $user->hasGroupPermission($group, RolePermissions::PROJECT_MANAGE);
    }

    /**
     * Determine if the user can update the project settings or background.
     * 
     * Triggered by: $project
     */
    public function update(User $user, Project $project): bool
    {
        if ($project->group->owner_id === $user->id) {
            return true;
        }

        if ($user->hasGroupPermission($project->group, RolePermissions::PROJECT_MANAGE)) {
            return true;
        }

        // Project members with MANAGE permission can update
        $projectMember = $project->members()->where('user_id', $user->id)->first();

        if ($projectMember && in_array(ProjectPermissions::MANAGE->value, $projectMember->permissions ?? [])) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the project.
     * 
     * Triggered by: $project
     */
    public function delete(User $user, Project $project): bool
    {
        if ($project->group->owner_id === $user->id) {
            return true;
        }

        return $user->hasGroupPermission($project->group, RolePermissions::PROJECT_MANAGE);
    }

    /**
     * Determine if the user can manage project memberships.
     * 
     * Handles both context states:
     * - [Project::class, $group] (Checking list/store before project exists)
     * - $project (Checking update/destroy on a specific project)
     */
    public function manageMembers(User $user, Group|Project $subject): bool
    {
        // Resolve the group context and the specific project
        $group = $subject instanceof Group ? $subject : $subject->group;
        $project = $subject instanceof Project ? $subject : null;

        // 1. Group Owner always has access
        if ($group->owner_id === $user->id) {
            return true;
        }

        // 2. Org Admins with PROJECT_MANAGE always have access
        if ($user->hasGroupPermission($group, RolePermissions::PROJECT_MANAGE)) {
            return true;
        }

        // 3. If acting on a specific project, allow if the member has MANAGE rights on it
        if ($project) {
            $projectMember = $project->members()->where('user_id', $user->id)->first();
            if ($projectMember && in_array(ProjectPermissions::MANAGE->value, $projectMember->permissions ?? [])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the user can manage project repository credentials (secrets).
     * 
     * Handles both context states:
     * - [Project::class, $group] (Setting up secrets during creation)
     * - $project (Viewing/updating secrets in settings)
     */
    public function manageSecrets(User $user, Group|Project $subject): bool
    {
        $group = $subject instanceof Group ? $subject : $subject->group;

        if ($group->owner_id === $user->id) {
            return true;
        }

        return $user->hasGroupPermission($group, RolePermissions::PROJECT_MANAGE);
    }
}
