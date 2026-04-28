<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * GroupController
 * 
 * Manages the lifecycle of Organizations and Teams.
 * Handles public discovery, personal dashboards, and hierarchical group management.
 */
class GroupController extends Controller
{
    /**
     * Display a paginated list of groups available for discovery.
     * 
     * This method implements high-performance fuzzy search using PostgreSQL's
     * pg_trgm extension. It filters results through the 'visibleTo' scope,
     * ensuring users only see public content or groups they have access to.
     * 
     * Search logic:
     *  - Uses the '%' operator for trigram distance filtering.
     *  - Ranks results using 'similarity' function to put the closest matches first.
     *  - Requires minimum of 3 characters to trigger search mode.
     * 
     * @param Request $request
     * @return ResourceCollection<GroupResource>
     */
    public function index(Request $request): ResourceCollection
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            'search' => ['nullable', 'string', 'min:3', 'max:255'],
        ]);

        $per_page = $request->integer('per_page', 15);

        $query = Group::visibleTo(Auth::id());

        if ($search = $request->search) {
            $query->whereRaw('name % ?', [$search])
                  ->orderByRaw('similarity(name, ?) DESC', [$search]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return GroupResource::collection(
            $query->paginate($per_page)->appends($request->query())
        );
    }

    /**
     * Create a new Organization or Team.
     * 
     * Logic:
     *  - Authorizes against 'createTeam' if parent_id is provided.
     *  - Triggers GroupObserver to initialize default RBAC roles.
     *  - Generates a placeholder icon if no file is uploaded.
     * 
     * @param StoreGroupRequest $request
     * @return GroupResource
     */
    public function store(StoreGroupRequest $request): GroupResource
    {
        if ($request->filled('parent_id')) {
            $parent = Group::findOrFail($request->parent_id);
            Gate::authorize('createTeam', $parent);
        }

        $data = $request->validated();
        $data['owner_id'] = $request->user()->id;

        $group = Group::create($data);

        if ($request->hasFile('icon')) {
            $group->saveCustomIcon($request->file('icon'));
        } else {
            $group->generateDefaultIcon();
        }

        $group->load('owner');

        if ($group->parent_id) {
            $group->load('parent');
        }
        

        return new GroupResource($group);
    }

    /**
     * Display specific group with context-aware loading.
     * 
     * Security: 'view' Policy. Supports stealth mode (404 on failure).
     * Context: Loads 'parent' for Teams and 'children' for Organizations.
     * 
     * @param Group $group
     * @return GroupResource
     */
    public function show(Group $group): GroupResource
    {
        if (!Gate::allows('view', $group)) {
            abort(404);
        }

        /**
         * @var User
         */
        $user = auth('sanctum')->user();

        if ($group->parent_id) {
            $group->load('parent');
        } else {
            $group->load(['children' => fn($q) => $q->visibleTo($user)]);
        }


        $group->load('owner');
        

        return new GroupResource($group);
    }

    /**
     * Update group metadata or branding.
     * 
     * Security: 'update' Policy.
     * 
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return GroupResource
     */
    public function update(UpdateGroupRequest $request, Group $group): GroupResource
    {
        Gate::authorize('update', $group);

        $group->update($request->validated());

        if ($request->hasFile('icon')) {
            $group->saveCustomIcon($request->file('icon'));
        }

        return new GroupResource($group->refresh());
    }

    /**
     * Delete specified group.
     * 
     * Security: 'delete' Policy.
     * 
     * @param Group $group
     * @return JsonResponse
     */
    public function destroy(Group $group): JsonResponse
    {
        Gate::authorize('delete', $group);

        $group->delete();

        return response()->json(['message' => 'Successfully deleted'], 200);
    }

    public function myGroups(Request $request): ResourceCollection
    {
        $groups = Group::memberOf($request->user())
                       ->latest()
                       ->get()
                       ->load('children')
                       ->load('owner');
        
        return GroupResource::collection($groups);
    }
}
