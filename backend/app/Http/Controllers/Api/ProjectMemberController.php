<?php

namespace App\Http\Controllers\Api;

use App\Enum\ProjectPermissions;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectMemberResource;
use App\Models\Group;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class ProjectMemberController extends Controller
{
    use HandlesStealthAuth;

     /**
     * Display a list of all members assigned to this project.
     * 
     * Security: 'view' Policy.
     */
    public function index(Group $group, Project $project): AnonymousResourceCollection
    {
        $this->authorizeStealth($group, 'view', 'You do not have access to this project.', [Project::class, $group]);

        // Eager load the related users to prevent N+1 query loops
        $members = $project->members()->with('user')->paginate(50);

        return ProjectMemberResource::collection($members);
    }

    /**
     * Add an existing Organization member to this specific project.
     * 
     * Security: 'manageMembers' Policy.
     */
    public function store(Request $request, Group $group, Project $project): ProjectMemberResource
    {
        $this->authorizeStealth($group, 'manageMembers', 'You cannot manage members.', [Project::class, $group]);

        // HARD MULTI-TENANCY RULE:
        // You cannot add a user to a Project unless they already belong to the Parent Org/Group!
        $request->validate([
            'user_id' => [
                'required',
                'uuid',
                // Ensures user_id is in group_members where group_id matches project->group_id
                Rule::exists('group_members', 'user_id')->where('group_id', $project->group_id),
            ],
            'permissions' => ['required', 'array'],
            'permissions.*' => [Rule::enum(ProjectPermissions::class)],
        ]);

        // Prevent duplicate project memberships
        $member = $project->members()->firstOrCreate(
            ['user_id' => $request->user_id],
            ['permissions' => $request->permissions]
        );

        return new ProjectMemberResource($member->load('user'));
    }

    /**
     * Update a member's project-specific permissions.
     * 
     * Security: 'manageMembers' Policy.
     */
    public function update(Request $request, Group $group, Project $project, ProjectMember $projectMember): ProjectMemberResource
    {
        $this->authorizeStealth($group, 'manageMembers', 'You cannot manage members.', [Project::class, $project]);

        $request->validate([
            'permissions' => ['required', 'array'],
            'permissions.*' => [Rule::enum(ProjectPermissions::class)],
        ]);

        // Prevent modifying the Group Owner's project permissions 
        // (they bypass checks and have god-mode anyway)
        if ($projectMember->user_id === $project->group->owner_id) {
            abort(403, "The Organization owner's project permissions are immutable.");
        }

        $projectMember->update([
            'permissions' => $request->permissions,
        ]);

        return new ProjectMemberResource($projectMember->load('user'));
    }

    /**
     * Remove a member from the project.
     * 
     * Security: 'manageMembers' Policy.
     */
    public function destroy(Group $group, Project $project, ProjectMember $projectMember): JsonResponse
    {
        $this->authorizeStealth($group, 'manageMembers', 'You cannot manage members.', [Project::class, $project]);

        // Prevent removing the Org owner from the project
        if ($projectMember->user_id === $project->group->owner_id) {
            abort(403, "The Organization owner cannot be removed from projects.");
        }

        $projectMember->delete();

        return response()->json(['message' => 'Member removed from project.']);
    }
}
