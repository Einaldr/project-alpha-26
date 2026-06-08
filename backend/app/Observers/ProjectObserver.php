<?php

namespace App\Observers;

use App\Models\Project;
use App\Services\GitService;
use Illuminate\Support\Facades\File;

class ProjectObserver
{
     /**
     * Create a new observer instance.
     * 
     * Laravel automatically injects your GitService here!
     */
    public function __construct(
        protected GitService $gitService
    ) {}

    /**
     * Handle the Project "created" event.
     * 
     * Automatically triggers the repository clone on creation.
     */
    public function created(Project $project): void
    {
        if (!empty($project->git_url)) {
            // Eager load secrets in case it is a private repository
            $project->load('secrets');

            // Trigger the clone inside your private repositories storage
            $this->gitService->clone($project);
        }
    }

    /**
     * Handle the Project "forceDeleted" event (Permanent Purge).
     * 
     * Cleans up the private repositories disk when a project is fully purged.
     */
    public function forceDeleted(Project $project): void
    {
        // Prevent leaving orphaned code directories on your server
        if (file_exists($project->git_path)) {
            File::deleteDirectory($project->git_path);
        }
    }
}
