<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupMemberController extends Controller
{
    public function groupMembers(Group $group): ResourceCollection
    {

    }

    public function inviteMember(Group $group, User $user, Request $request): GroupMember
    {

    }

    public function kickMember(Group $group, User $user, Request $request): JsonResponse
    {
        
    }
}
