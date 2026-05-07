<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupRoleResource;
use App\Models\Group;
use App\Models\GroupRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupRoleController extends Controller
{
    public function index(Request $request, Group $group): ResourceCollection
    {
        abort(501, "Work in progress");
        // TODO: Implement policy

        // TODO: Implement trigram search
    }

    public function show(Request $request, Group $group, GroupRole $groupRole): GroupRoleResource
    {
        abort(501, "Work in progress");
        // TODO: Implement policy

    }

    public function store(): GroupRoleResource
    {
        abort(501, "Work in progress");
        // TODO: Implement StoreGroupRoleRequest

        // TODO: Implement policy
    }

    public function update(): GroupRoleResource
    {
        abort(501, "Work in progress");
        // TODO: Implement UpdateGroupRoleRequest

        // TODO: Implement policy
    }

    public function destroy(Group $group, GroupRole $groupRole): JsonResponse
    {
        abort(501, "Work in progress");
        // TODO: Implement policy

        // TODO: Implement GroupRole resource
    }
}
