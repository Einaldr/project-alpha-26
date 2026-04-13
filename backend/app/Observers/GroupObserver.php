<?php

namespace App\Observers;

use App\Enum\RolePermissions;
use App\Models\Group;

class GroupObserver
{
    /**
     * Handle the group "created" event.
     */
    public function created(Group $group): void
    {
        $ownerRole = $group->roles()->create([
            'name' => 'Owner',
            'permissions' => RolePermissions::cases(), // All permissions from the Enum
        ]);

        // 2. Create the 'Admin' Role (Management permissions)
        $group->roles()->create([
            'name' => 'Admin',
            'permissions' => [
                RolePermissions::GROUP_UPDATE->value,
                RolePermissions::MEMBER_INVITE->value,
                RolePermissions::MEMBER_KICK->value,
                RolePermissions::ROLES_MANAGE->value,
                RolePermissions::PROJECT_MANAGE->value,
                RolePermissions::PROJECT_INVITE->value,
                RolePermissions::REPOSITORY_MANAGE->value,
            ],
        ]);

        // 3. Create the 'Member' Role (Basic permissions)
        $group->roles()->create([
            'name' => 'Member',
            'permissions' => [
                RolePermissions::PROJECT_INVITE->value,
            ],
        ]);

        // 4. Create the Membership record for the creator
        // This links the user to the group in the 'group_members' table
        $member = $group->members()->create([
            'user_id' => $group->owner_id,
        ]);

        // 5. Attach the 'Owner' role to this new member
        // This populates your 'pivot_group_roles' table
        $member->roles()->attach($ownerRole->id);
    }

    /**
     * Handle the group "updated" event.
     */
    public function updated(group $group): void
    {
        //
    }

    /**
     * Handle the group "deleted" event.
     */
    public function deleted(group $group): void
    {
        //
    }

    /**
     * Handle the group "restored" event.
     */
    public function restored(group $group): void
    {
        //
    }

    /**
     * Handle the group "force deleted" event.
     */
    public function forceDeleted(group $group): void
    {
        //
    }
}
