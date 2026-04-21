<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('public')->deleteDirectory('groups');
        Storage::disk('public')->makeDirectory('groups');

        User::factory()->create([
            'name' => 'Einaldr',
            'email' => 'einaldr@test.com',
            'password' => 'Password123'
        ]);

        User::factory(50)->create();

        User::factory(5)->banned()->create();

        User::factory(5)->deleted()->create();
    }
}
