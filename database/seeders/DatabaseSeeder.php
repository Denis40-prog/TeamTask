<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'Moi',
            'email' => 'denis.chevanne@hotmail.fr',
            'password' => Hash::make('testtest'),
            'role' => 'admin',
        ]);
        \App\Models\Team::factory()->create([
            'name' => 'Team-1',
        ]);
        \App\Models\TeamUser::factory()->create([
            'team_id' => 1,
            'user_id' => 1,
            'role_in_team' => 'Admin',
        ]);
        \App\Models\Project::factory()->create([
            'name' => 'Project-1',
            'description' => 'description project 1',
            'owner_id'=> 1,
        ]);
        \App\Models\Task::factory()->create([
            'title' => 'Task-1',
            'project_id' => 1,
            'assignee_id' => 1,
        ]);
        \App\Models\Comment::factory()->create([
            'content' => 'Comment content',
            'task_id' => 1,
            'project_id' => 1,
            'user_id' => 1,
        ]);
        \App\Models\Notification::factory()->create([
            'content' => 'Notification content',
            'type' => 'type ?',
            'user_id' => 1,
        ]);



        // \App\Models\User::factory(10)->create();

        // \App\Models\Comment::factory(10)->create();
        // \App\Models\Notification::factory(10)->create();
        // \App\Models\Project::factory(10)->create();
        // \App\Models\Task::factory(10)->create();
        // \App\Models\Team::factory(10)->create();

    }
}
