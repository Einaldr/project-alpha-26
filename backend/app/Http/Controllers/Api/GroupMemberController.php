<?php

namespace App\Http\Controllers\Api;

use App\Enum\RolePermissions;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupMemberController extends Controller
{
    public function groupMembers(Group $group, Request $request): ResourceCollection
    {
        if (!$request->user()->hasGroupPermission($group, RolePermissions::GROUP_VIEW)) {
            if ($group->parent_id) {
                if ($group->is_private_child) {
                    abort(404, "Group doesn't exist.");
                }
            }
            abort(403, "You don't have access to the group members.");
        }

        // TODO: Create GroupMember/s resource

    }

    public function inviteMember(Group $group, User $user, Request $request): GroupMember
    {

    }

    public function kickMember(Group $group, User $user, Request $request): JsonResponse
    {
        
    }
}
