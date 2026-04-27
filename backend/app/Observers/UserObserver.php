<?php

namespace App\Observers;

use App\Enum\GroupType;
use App\Models\Group;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the user "created" event.
     * 
     * Automatically intializes a private workspace instance for the new user
     * and triggers the default icon generation for that group.
     * 
     * Note:
     * It will subsequently trigger GroupObserver to initialize roles and memberships.
     * 
     * @param \App\Models\User $user The user that has been created.
     * @return void
     */
    public function created(User $user): void
    {
        $group = Group::create([
            'name' => $user->name . "'s Workspace",
            'type' => GroupType::INDIVIDUAL,
            'owner_id' => $user->id,
        ]);

        $group->generateDefaultIcon();
    }
}
