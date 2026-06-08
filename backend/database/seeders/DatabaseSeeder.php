<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Process;

class DatabaseSeeder extends Seeder
{ 
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('icons')->deleteDirectory('groups');
        Storage::disk('images')->deleteDirectory('groups');
        Storage::disk('repositories')->deleteDirectory('groups');

        $mockGitUrl = $this->createMockRepositoryOnDisk();

        $testingUser = User::factory()->create([
            'name' => 'Einaldr',
            'email' => 'einaldr@test.com',
            'password' => 'Password123'
        ]);

        $workspace = $testingUser->personalGroup;

        if ($workspace) {
            $workspace->projects()->create([
                'name' => 'My Personal Project',
                'description' => 'This is my private personal workspace project.',
                'git_url' => $mockGitUrl, // 💡 The Magic: Clones ITSELF!
                'default_branch' => 'main', // Ensure this matches your active local branch
            ]);
        }

        $users = User::factory(75)->create();

        User::factory(5)->banned()->create();

        User::factory(5)->deleted()->create();

        $groups = Group::factory(10)->create();
        
        $teams = Group::factory(2)->team()->create();

        GroupMember::factory()
                   ->count(20)
                   ->recycle($users)
                   ->recycle($groups)
                   ->withRole('Member')
                   ->create();

        GroupMember::factory()
                   ->count(5)
                   ->recycle($users)
                   ->recycle($groups)
                   ->withRole('Admin')
                   ->create();

        GroupMember::factory()
                   ->count(5)
                   ->recycle($teams)
                   ->recycle($users)
                   ->withRole('Member')
                   ->create();

        $testingGroup = Group::factory()->create([
            'name'=> "Testing Group",
            'owner_id' => $testingUser->id,
            'billing_email' => null,
        ]);

        $testingGroup->projects()->create([
            'name' => 'Project Alpha',
            'description' => 'This is the main collaborative testing group project.',
            'git_url' => $mockGitUrl, // 💡 Clones ITSELF!
            'default_branch' => 'main',
        ]);

        GroupMember::factory()
                   ->count(5)
                   ->recycle($users)
                   ->withRole('Member')
                   ->create(['group_id' => $testingGroup->id]);
    }

    /**
     * Dynamically build a safe, temporary Git repository inside storage
     * with two branches (main/dev) for risk-free local testing.
     */
    protected function createMockRepositoryOnDisk(): string
    {
        $mockPath = storage_path('app/temp_mock_source');

        if (file_exists($mockPath)) {
            File::deleteDirectory($mockPath);
        }

        mkdir($mockPath, 0755, true);

        // A. Setup git config inside the temporary sandbox
        Process::path($mockPath)->run('git init');
        Process::path($mockPath)->run('git config user.email "seeder@projectalpha.local"');
        Process::path($mockPath)->run('git config user.name "Database Seeder"');
        Process::path($mockPath)->run('git checkout -b main');

        // B. Create safe default files for the 'main' branch
        file_put_contents($mockPath . '/README.md', "# Project Alpha\n\nWelcome to your safe repository sandbox.");
        mkdir($mockPath . '/src');
        file_put_contents($mockPath . '/src/index.js', "console.log('Hello from Main branch!');");

        // C. Commit 'main'
        Process::path($mockPath)->run('git add .');
        Process::path($mockPath)->run('git commit -m "Initial commit on main branch"');

        // D. Create a secondary branch 'dev' with different file contents
        Process::path($mockPath)->run('git checkout -b dev');
        file_put_contents($mockPath . '/src/index.js', "console.log('Hello from Dev branch!');");
        file_put_contents($mockPath . '/DEVELOPMENT.md', "# Dev Docs\n\nThis file is only visible on the dev branch.");
        
        // E. Commit 'dev'
        Process::path($mockPath)->run('git add .');
        Process::path($mockPath)->run('git commit -m "Feature: Added development docs"');

        // F. Switch back to main so the clone defaults to main
        Process::path($mockPath)->run('git checkout main');

        return $mockPath;
    }
}
