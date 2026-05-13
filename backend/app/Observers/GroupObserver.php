<?php

namespace App\Observers;

use App\Enum\GroupType;
use App\Enum\RolePermissions;
use App\Models\Group;

class GroupObserver
{
    /**
     * Handle the group "created" event.
     * 
     * Automatically add default roles to the group that's being created
     * and auto assign Owner role to the group's owner.
     */
    public function created(Group $group): void
    {
        $group->initialize();
    }
}
