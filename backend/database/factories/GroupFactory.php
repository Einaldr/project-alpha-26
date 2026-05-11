<?php

namespace Database\Factories;

use App\Enum\GroupType;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->faker->company(),
            'type' => GroupType::ORG,
            'owner_id' => User::factory(),
            'parent_id' => null,
            'is_private_child' => false,
            'billing_email' => $this->faker->companyEmail(),
            'icon_path' => null,
        ];
    }

    public function team(?Group $parent = null): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->jobTitle() . ' Team',
            'type' => GroupType::TEAM,
            'parent_id' => $parent->id ?? Group::factory()->org(),
        ]);
    }

    public function org(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => GroupType::ORG,
            'parent_id' => null,
        ]);
    }

    public function individual(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->name() . "'s Workspace",
            'type' => GroupType::INDIVIDUAL,
            'parent_id' => null,
        ]);
    }

    #[Override]
    public function configure(): static
    {
        return $this->afterCreating(function (Group $group) {
            if (empty($group->icon_path)) {
                $group->generateDefaultIcon();
            }
        });
    }
}
