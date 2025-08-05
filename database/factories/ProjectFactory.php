<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-5 years', 'now');
        $hasEnded = $this->faker->boolean(50);

        $endDate = $hasEnded
            ? $this->faker->dateTimeBetween($startDate, 'now')
            : null;

        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $endDate === null ? 'active' : 'archived',
            'owner_id' => \App\Models\User::factory(),
            'team_id' => \App\Models\Team::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

}
