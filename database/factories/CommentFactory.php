<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Créer aléatoirement soit un commentaire de tâche soit un commentaire de projet
        $isTaskComment = $this->faker->boolean(70); // 70% de chance d'être un commentaire de tâche

        if ($isTaskComment) {
            return [
                'content' => $this->faker->paragraph(),
                'task_id' => \App\Models\Task::inRandomOrder()->first()?->id ?? \App\Models\Task::factory(),
                'project_id' => null,
                'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            return [
                'content' => $this->faker->paragraph(),
                'task_id' => null,
                'project_id' => \App\Models\Project::inRandomOrder()->first()?->id ?? \App\Models\Project::factory(),
                'user_id' => \App\Models\User::inRandomOrder()->first()?->id ?? \App\Models\User::factory(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    }
}
