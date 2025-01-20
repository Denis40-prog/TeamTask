<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => \App\Models\Team::factory()->create()->id,
            'user_id' => \App\Models\User::factory()->create()->id,
            'role_in_team' => $this->faker->randomElement(['Admin', 'Membre']),
        ];
    }
}
