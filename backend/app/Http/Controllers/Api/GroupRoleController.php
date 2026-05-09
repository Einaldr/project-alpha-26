<?php

namespace App\Http\Controllers\Api;

use App\Enum\RolePermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRoleRequest;
use App\Http\Requests\UpdateGroupRoleRequest;
use App\Http\Resources\GroupRoleResource;
use App\Models\Group;
use App\Models\GroupRole;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GroupRoleController extends Controller
{
    use HandlesStealthAuth;

    public function index(Request $request, Group $group): ResourceCollection
    {
        $this->authrizeStealth($group, 'anyView', 'You are not authorized to list roles');

        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
            'order' => ['nullable', 'string', 'min:3', 'max:3'],
        ]);

        $per_page = $request->integer('per_page', 15);
        $order = $request?->order == 'asc' ? 'asc' : 'desc';

        $query = GroupRole::query();

        if ($search = $request->search) {
            $query->whereRaw('name % ?', [$search])
                  ->orderByRaw('similarity(name, ?) DESC', [$search]);
        } else {
            $query->orderBy('created_at', $order);
        }

        return GroupRole::collection(
            $query->paginate($per_page)->appends($request->query())
        );
    }

    public function show(Group $group, GroupRole $groupRole): GroupRoleResource
    {
        $this->authrizeStealth($group, 'view', "You are not authorized to see role settings");

        return new GroupRoleResource($groupRole);
    }

    public function store(StoreGroupRoleRequest $request, Group $group): GroupRoleResource
    {
        $this->authrizeStealth($group, 'create', 'You are not authorized to create roles');

        $role = GroupRole::create([
            'name' => $request->name,
            'group_id' => $group->id,
            'permissions' => array_map(fn($val) => RolePermissions::from($val),
                                       $request->permissions
            )
        ]);

        return new GroupRoleResource($role);
    }

    public function update(UpdateGroupRoleRequest $request, Group $group, GroupRole $role): GroupRoleResource
    {
        $this->authrizeStealth($group, 'update', 'You are not authorized to edit roles');

        if ($request->name) {
            $role->update([
                'name' => $request->name,
            ]);
        }

        if ($request->permissions) {
            $role->update([
                'permissions' => array_map(fn($val) => RolePermissions::from($val), $request->permissions),
            ]);
        }

        return new GroupRoleResource($role->refresh());
    }

    public function destroy(Group $group, GroupRole $role): JsonResponse
    {
        $this->authrizeStealth($group, 'delete', 'You are not authorized to delete roles');

        $role->delete();

        return response()->json(['message' => 'Role successfully deleted']);
    }
}
