<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Project;
use App\Services\GitService;
use App\Traits\HandlesStealthAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProjectFileController extends Controller
{
    use HandlesStealthAuth;

    public function __construct(
        protected GitService $gitService
    ) {}

    /**
     * List files and folders in a specific subdirectory (Lazy Loading).
     * 
     * Endpoint: GET /api/projects/{project}/files?path=src/components
     */
    public function index(Request $request, Group $group, Project $project): JsonResponse
    {
        // 1. Security Check: Can the user view this project?
        $this->authorizeStealth($group, 'view', 'You do not have permission to browse files.', [Project::class, $project]);

        // 2. Resolve the target path
        $relativeQueryPath = ltrim($request->query('path', ''), '/');
        $targetPath = $project->git_path . '/' . $relativeQueryPath;

        // 3. HARD SECURITY: Prevent Directory Traversal exploits (../)
        $realTarget = realpath($targetPath);
        $realBase = realpath($project->git_path);

        // If the path doesn't exist, or has escaped the repository sandbox
        if (!$realTarget || !str_starts_with($realTarget, $realBase)) {
            abort(403, "Access Denied: Invalid directory path.");
        }

        // 4. Read the flat contents of this directory
        $directories = File::directories($realTarget);
        $files = File::files($realTarget);

        $contents = [];

        // 5. Map Directories
        foreach ($directories as $dir) {
            $contents[] = [
                'name' => basename($dir),
                'type' => 'directory',
                'path' => str_replace($realBase . '/', '', $dir),
            ];
        }

        // 6. Map Files
        foreach ($files as $file) {
            $contents[] = [
                'name' => $file->getFilename(),
                'type' => 'file',
                'path' => str_replace($realBase . '/', '', $file->getRealPath()),
                'size' => $file->getSize(), // In bytes
            ];
        }

        return response()->json([
            'current_path' => $relativeQueryPath,
            'current_branch' => $this->gitService->getCurrentBranch($project),
            'data' => $contents
        ]);
    }

    /**
     * Get the raw text content of a specific file.
     * 
     * Endpoint: GET /api/projects/{project}/files/show?path=src/App.tsx
     */
    public function show(Request $request, Group $group, Project $project): JsonResponse
    {
        $this->authorizeStealth($group, 'view', 'You do not have permission to view files.', [Project::class, $project]);

        $relativeQueryPath = ltrim($request->query('path', ''), '/');
        if (empty($relativeQueryPath)) {
            return response()->json(['message' => 'File path is required.'], 400);
        }

        $targetPath = $project->git_path . '/' . $relativeQueryPath;

        // HARD SECURITY: Prevent Directory Traversal
        $realTarget = realpath($targetPath);
        $realBase = realpath($project->git_path);

        if (!$realTarget || !str_starts_with($realTarget, $realBase) || !is_file($realTarget)) {
            abort(403, "Access Denied: Invalid file path.");
        }

        // File Metadata
        $extension = File::extension($realTarget);
        $mime = File::mimeType($realTarget);
        $size = File::size($realTarget);

        // Binary File Guard: Don't try to read images/zips as text
        $isText = str_starts_with($mime, 'text/') || in_array($extension, [
            'json', 'md', 'ts', 'tsx', 'js', 'jsx', 'yml', 'yaml', 'config', 'lock'
        ]);

        if (!$isText) {
            return response()->json([
                'name' => basename($realTarget),
                'path' => $relativeQueryPath,
                'extension' => $extension,
                'size' => $size,
                'is_binary' => true,
                'content' => null, // Frontend will show a download option
            ]);
        }

        // Read raw contents
        $content = File::get($realTarget);

        return response()->json([
            'name' => basename($realTarget),
            'path' => $relativeQueryPath,
            'extension' => $extension,
            'size' => $size,
            'is_binary' => false,
            'content' => $content,
        ]);
    }

    /**
     * List all available local and remote branches.
     * 
     * Endpoint: GET /api/projects/{project}/branches
     */
    public function branches(Group $group, Project $project): JsonResponse
    {
        $this->authorizeStealth($group, 'view', "You can not view branches.", [Project::class, $project]);

        $branches = $this->gitService->getBranches($project);

        return response()->json([
            'current' => $this->gitService->getCurrentBranch($project),
            'branches' => $branches
        ]);
    }

    /**
     * Pull the latest changes for the current branch.
     * 
     * Endpoint: POST /api/projects/{project}/pull
     */
    public function pull(Group $group, Project $project): JsonResponse
    {
        // Require update/write permissions to trigger a pull
        $this->authorizeStealth($group, 'update', 'You cannot pull repository changes.', [Project::class, $project]);

        $project->load('secrets'); // Eager load credentials

        $success = $this->gitService->pull($project);

        if (!$success) {
            return response()->json(['message' => 'Failed to pull repository changes.'], 500);
        }

        return response()->json(['message' => 'Repository successfully updated.']);
    }

    /**
     * Checkout a specific branch.
     * 
     * Endpoint: POST /api/projects/{project}/checkout
     */
    public function checkout(Request $request, Group $group, Project $project): JsonResponse
    {
        $this->authorizeStealth($group, 'update', 'You cannot switch branches.', [Project::class, $project]);

        $request->validate([
            'branch' => ['required', 'string', 'max:255'],
        ]);

        $success = $this->gitService->checkout($project, $request->branch);

        if (!$success) {
            return response()->json(['message' => "Failed to checkout branch '{$request->branch}'."], 500);
        }

        return response()->json([
            'message' => "Successfully switched to branch '{$request->branch}'.",
            'current_branch' => $request->branch
        ]);
    }
}
