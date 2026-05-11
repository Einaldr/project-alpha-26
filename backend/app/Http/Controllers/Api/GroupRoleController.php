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

/**
 * GroupRoleController
 * 
 * Manages the lifecycle of group roles' lifecycle.
 * Handles displaying, creating and editing group roles.
 */
class GroupRoleController extends Controller
{
    use HandlesStealthAuth;

    /**
     * Display a paginated list of group's roles.
     * 
     * @param Request $request
     * @param Group $group
     * @return ResourceCollection
     */
    public function index(Request $request, Group $group): ResourceCollection
    {
        $this->authorizeStealth($group, 'viewAny', 'You are not authorized to list roles', [GroupRole::class, $group]);

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

    /**
     * Display individual role's information.
     * 
     * @param Group $group
     * @param GroupRole $groupRole
     * @return GroupRoleResource
     */
    public function show(Group $group, GroupRole $groupRole): GroupRoleResource
    {
        $this->authorizeStealth($group, 'view', "You are not authorized to see role settings", [GroupRole::class, $group]);

        return new GroupRoleResource($groupRole);
    }

    /**
     * Create a new group role.
     * 
     * @param StoreGroupRoleRequest $request
     * @param Group $group
     * @return GroupRoleResource
     */
    public function store(StoreGroupRoleRequest $request, Group $group): GroupRoleResource
    {
        $this->authorizeStealth($group, 'create', 'You are not authorized to create roles', [GroupRole::class, $group]);

        $role = GroupRole::create([
            'name' => $request->name,
            'group_id' => $group->id,
            'permissions' => array_map(fn($val) => RolePermissions::from($val),
                                       $request->permissions
            )
        ]);

        return new GroupRoleResource($role);
    }

    /**
     * Update a group role.
     * 
     * @param UpdateGroupRoleRequest $request
     * @param Group $group
     * @param GroupRole $role
     * @return GroupRoleResource
     */
    public function update(UpdateGroupRoleRequest $request, Group $group, GroupRole $role): GroupRoleResource
    {
        $this->authorizeStealth($group, 'update', 'You are not authorized to edit roles', [GroupRole::class, $group]);

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

    /**
     * Delete a group role
     * 
     * @param Group $group
     * @param GroupRole $role
     * @return JsonResponse
     */
    public function destroy(Group $group, GroupRole $role): JsonResponse
    {
        $this->authorizeStealth($group, 'delete', 'You are not authorized to delete roles', [GroupRole::class, $group]);

        $role->delete();

        return response()->json(['message' => 'Role successfully deleted']);
    }
}
