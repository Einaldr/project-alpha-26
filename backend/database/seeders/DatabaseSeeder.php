<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{ 
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('groups');
        Storage::disk('public')->makeDirectory('groups');

        $testingUser = User::factory()->create([
            'name' => 'Einaldr',
            'email' => 'einaldr@test.com',
            'password' => 'Password123'
        ]);

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
            'name'=> "Testing Group Group",
            'owner_id' => $testingUser->id,
            'billing_email' => null,
        ]);

        GroupMember::factory()
                   ->count(5)
                   ->recycle($users)
                   ->withRole('Member')
                   ->create(['group_id' => $testingGroup->id]);
    }
}
