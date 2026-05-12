<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GroupMember>
 */
class GroupMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
        ];
    }

    public function withRole(string $roleName = 'Member'): static
    {
        return $this->afterCreating(function (GroupMember $member) use ($roleName) {
            $role = GroupRole::where('group_id', $member->group_id)
                             ->where('name', $roleName)
                             ->first();

            if ($role) {
                $member->roles()->syncWithoutDetaching([$role->id]);
            }
        });
    }
}
