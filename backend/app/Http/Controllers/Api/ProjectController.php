<?php

namespace App\Http\Controllers\Api;

use App\Enum\ProjectPermissions;
use App\Enum\RolePermissions;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Group;
use App\Models\Project;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    use HandlesStealthAuth;

    /**
     * Display a list of projects in a group.
     */
    public function index(Request $request, Group $group): AnonymousResourceCollection
    {
        $this->authorizeStealth($group, 'view');

        $projects = $group->projects()
            ->visibleTo($request->user())
            ->latest()
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project.
     */
    public function store(StoreProjectRequest $request, Group $group): ProjectResource
    {
        // 1. Authorization: check if they can create projects in this Org
        $this->authorizeStealth($group, 'createProject');

        $project = $group->projects()->create($request->validated());

        // 3. Unified Secrets Creation: Save secrets under the same transaction
        if ($request->filled('auth_type')) {
            $project->secrets()->create([
                'auth_type' => $request->auth_type,
                'access_token' => $request->access_token,
            ]);
        }

        // 4. Assign the creator as the first member
        $project->members()->create([
            'user_id' => $request->user()->id,
            'permissions' => [
                ProjectPermissions::READ->value, 
                ProjectPermissions::MANAGE->value
            ],
        ]);

        // Return the project resource with loaded relations (this will include our secure secrets resource)
        return new ProjectResource($project->load(['secrets', 'members.user']));
    }

    /**
     * Display the specified project.
     */
    public function show(Group $group, Project $project): ProjectResource
    {
        $this->authorizeStealth($group, 'view', "You don't have access to the project",[Project::class, $project]);

        return new ProjectResource($project->load(['group', 'members.user']));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Group $group, Project $project): ProjectResource
    {
        $this->authorizeStealth($group, 'update', "You can not update project",[Project::class, $project]);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:64'],
            'description' => ['nullable', 'string'],
            'default_branch' => ['nullable', 'string'],
            'background_image' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:5000'],
        ]);

        $project->update($validated);

        if ($request->hasFile('background_image')) {
            $project->saveCustomBackground($request->file('background_image'));
        }

        return new ProjectResource($project->fresh());
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Group $group, Project $project): JsonResponse
    {
        $this->authorizeStealth($group, 'delete', "You can not delete the project",[Project::class, $project]);

        $project->delete();

        return response()->json(['message' => 'Project successfully deleted.']);
    }

    /**
     * Return user's permissions to the specified project.
     */
    public function myPermissions(Request $request, Group $group, Project $project): JsonResponse
    {
        $this->authorizeStealth($group, 'view', "You don't have access to the project",[Project::class, $project]);

        $user = $request->user();

        $isOrgManager = $project->group->owner_id === $user->id || $user->hasGroupPermission($project->group, RolePermissions::PROJECT_MANAGE);

        if ($isOrgManager) {
            return response()->json([
                'permissions' => ProjectPermissions::values()
            ]);
        }

        $projectMember = $project->members()->where('user_id', $user->id)->firstOrFail();
        return response()->json([
            'permissions' => $projectMember->permissions
        ]);
    }
}
