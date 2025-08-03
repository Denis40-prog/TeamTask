<?php

namespace Database\Seeders;

use App\Models\ChallengeParticipant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChallengeParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ChallengeParticipant::factory()->count(10)->create();
    }
}
