<?php

namespace Database\Seeders;

use App\Models\TeamUser;
use App\Models\User;
use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Exclure l'admin du site du seeding automatique
        $users = User::where('role', '!=', User::ROLE_ADMIN)->get();
        $teams = Team::all();

        // Assigner des rôles variés aux utilisateurs dans les équipes
        foreach ($teams as $team) {
            $teamUsers = $users->random(rand(3, 6));

            foreach ($teamUsers as $index => $user) {
                // Éviter les doublons
                if (TeamUser::where('user_id', $user->id)->where('team_id', $team->id)->exists()) {
                    continue;
                }

                if ($index === 0) {
                    // Premier utilisateur = admin de l'équipe
                    $role = TeamUser::TEAM_ROLE_ADMIN;
                } elseif ($index === 1 && rand(0, 1)) {
                    // Deuxième utilisateur = parfois RH
                    $role = TeamUser::TEAM_ROLE_RH;
                } else {
                    // Les autres = utilisateurs normaux
                    $role = TeamUser::TEAM_ROLE_USER;
                }

                TeamUser::create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                    'role' => $role,
                ]);
            }
        }
    }
}
