<?php

namespace Database\Seeders;

use App\Models\TaskUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskUser::factory()->count(10)->create();
    }
}
