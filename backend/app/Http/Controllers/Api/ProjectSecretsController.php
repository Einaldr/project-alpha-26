<?php

namespace App\Http\Controllers\Api;

use App\Enum\GitAuthType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectSecretsResource;
use App\Models\Group;
use App\Models\Project;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectSecretsController extends Controller
{
    use HandlesStealthAuth;

     /**
     * View the public credential settings for a project.
     */
    public function show(Group $group, Project $project): ProjectSecretsResource
    {
        $this->authorizeStealth($group, 'manageSecrets', 'You cannot view repository credentials.', [Project::class, $group]);

        // Get existing secrets or return a blank placeholder
        $secrets = $project->secrets ?: $project->secrets()->create([
            'auth_type' => 'none',
            'access_token' => null
        ]);

        return new ProjectSecretsResource($secrets);
    }

    /**
     * Update the Personal Access Token (PAT) or connection type.
     */
    public function update(Request $request, Group $group, Project $project): ProjectSecretsResource
    {
        $this->authorizeStealth($group, 'manageSecrets', 'You cannot update repository credentials.', [Project::class, $group]);

        $request->validate([
            'auth_type' => ['required', Rule::enum(GitAuthType::class)],
            'access_token' => ['nullable', 'string', 'max:255', 'required_if:auth_type,http'],
        ]);

        $secrets = $project->secrets ?: $project->secrets()->create();

        $secrets->update([
            'auth_type' => $request->auth_type,
            'access_token' => $request->access_token,
        ]);

        return new ProjectSecretsResource($secrets);
    }

    /**
     * Revoke the access token (revert to anonymous/none).
     */
    public function destroy(Group $group, Project $project): JsonResponse
    {
        $this->authorizeStealth($group, 'manageSecrets', "You can not manage repository secrets", [Project::class, $group]);

        if ($project->secrets) {
            $project->secrets->update([
                'auth_type' => 'none',
                'access_token' => null
            ]);
        }

        return response()->json(['message' => 'Repository credentials successfully revoked.']);
    }
}
