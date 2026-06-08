<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Process;

class GitService
{
    /**
     * Clone a remote repository to the project's private isolated storage directory.
     */
    public function clone(Project $project): bool
    {
        $parentDirectory = dirname($project->git_path);
        if (!file_exists($parentDirectory)) {
            mkdir($parentDirectory, 0755, true);
        }

        // Inject PAT (Personal Access Token) safely if configured
        $cloneUrl = $this->getAuthenticatedUrl($project);

        // We use escapeshellarg on the URL and path for shell safety
        $urlArg = escapeshellarg($cloneUrl);
        $pathArg = escapeshellarg($project->git_path);

        $process = Process::run("git clone --quiet {$urlArg} {$pathArg}");

        return $process->successful();
    }

    /**
     * Pull the latest changes for the project's currently checked-out branch.
     */
    public function pull(Project $project): bool
    {
        if (!file_exists($project->git_path)) {
            return $this->clone($project);
        }

        $process = Process::path($project->git_path)->run("git pull");

        if ($process->successful()) {
            $project->update(['last_pulled_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Checkout a specific local or remote branch.
     */
    public function checkout(Project $project, string $branch): bool
    {
        $branchArg = escapeshellarg($branch);

        $process = Process::path($project->git_path)->run("git checkout {$branchArg}");

        return $process->successful();
    }

    /**
     * Get the currently checked-out branch name.
     */
    public function getCurrentBranch(Project $project): ?string
    {
        $process = Process::path($project->git_path)->run("git branch --show-current");

        return $process->successful() ? trim($process->output()) : null;
    }

    /**
     * List all available branches (both local and remote).
     */
    public function getBranches(Project $project): array
    {
        // Fetch remote branches list first to ensure we are up to date
        Process::path($project->git_path)->run("git fetch --prune");

        $process = Process::path($project->git_path)->run("git branch -a");

        if (!$process->successful()) {
            return [];
        }

        // Parse "git branch -a" output into a clean array of unique branch names
        return collect(explode("\n", $process->output()))
            ->map(function ($line) {
                // Remove the active asterisk, whitespace, and remote prefixes
                $line = trim($line);
                $line = ltrim($line, '* ');
                return str_replace('remotes/origin/', '', $line);
            })
            ->filter(fn($name) => !empty($name) && !str_contains($name, 'HEAD ->'))
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Inject Personal Access Token safely into HTTPS URLs during process execution.
     */
    protected function getAuthenticatedUrl(Project $project): string
    {
        $url = $project->git_url;

        // Ensure secrets are loaded
        $secrets = $project->relationLoaded('secrets') ? $project->secrets : $project->secrets()->first();

        if (!$secrets || empty($secrets->access_token)) {
            return $url;
        }

        // Format: https://{token}@github.com/owner/repo.git
        if (str_starts_with($url, 'https://')) {
            $cleanUrl = str_replace('https://', '', $url);
            return "https://{$secrets->access_token}@{$cleanUrl}";
        }

        return $url;
    }
}