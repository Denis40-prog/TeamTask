<?php

namespace App\Providers;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Permission pour voir la liste des équipes avec accès wellness
        Gate::define('viewWellness', function (User $user) {
            // Admin du site peut tout voir
            if ($user->role === User::ROLE_ADMIN) {
                return true;
            }

            // Utilisateur normal : doit avoir au moins une équipe avec accès wellness
            return TeamUser::where('user_id', $user->id)
                ->whereIn('role', [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH])
                ->exists();
        });

        // Permission pour voir le wellness d'une équipe spécifique
        Gate::define('viewWellnessForTeam', function (User $user, Team $team) {
            // Admin du site peut tout voir
            if ($user->role === User::ROLE_ADMIN) {
                return true;
            }

            // Utilisateur normal : doit être admin ou RH de cette équipe
            return TeamUser::where('user_id', $user->id)
                ->where('team_id', $team->id)
                ->whereIn('role', [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH])
                ->exists();
        });

        // Permission pour gérer les utilisateurs (réservé aux admins du site)
        Gate::define('manageUsers', function (User $user) {
            return $user->role === User::ROLE_ADMIN;
        });

        // =====================================
        // PERMISSIONS DE SUPPRESSION
        // =====================================

        // Permission pour supprimer une équipe
        Gate::define('deleteTeam', function (User $user, Team $team) {
            return $user->canDeleteTeam($team);
        });

        // Permission pour supprimer un projet
        Gate::define('deleteProject', function (User $user, $project) {
            return $user->canDeleteProject($project);
        });

        // Permission pour supprimer une tâche
        Gate::define('deleteTask', function (User $user, $task) {
            return $user->canDeleteTask($task);
        });

        // Permission pour supprimer un commentaire
        Gate::define('deleteComment', function (User $user, $comment) {
            return $user->canDeleteComment($comment);
        });
    }
}
