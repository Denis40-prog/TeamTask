<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;

trait HasTeamPermissions
{
    /**
     * Check if user has any role in a team
     */
    public function hasRoleInTeam(Team $team, $role = null): bool
    {
        $query = $this->teams()->where('teams.id', $team->id);

        if ($role) {
            $query->wherePivot('role', $role);
        }

        return $query->exists();
    }

    /**
     * Get user's role in a specific team
     */
    public function getRoleInTeam(Team $team): ?string
    {
        $teamUser = $this->teams()->where('teams.id', $team->id)->first();
        return $teamUser ? $teamUser->pivot->role : null;
    }

    /**
     * Check if user is team admin for a specific team
     */
    public function isTeamAdminFor(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin has all permissions
        }

        return $this->hasRoleInTeam($team, TeamUser::TEAM_ROLE_ADMIN);
    }

    /**
     * Check if user is RH for a specific team
     */
    public function isTeamRHFor(Team $team): bool
    {
        return $this->hasRoleInTeam($team, TeamUser::TEAM_ROLE_RH);
    }

    /**
     * Check if user has wellness access for a specific team
     */
    public function hasWellnessAccessFor(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin has all permissions
        }

        $role = $this->getRoleInTeam($team);
        return in_array($role, [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH]);
    }

    /**
     * Check if user can manage team members for a specific team
     */
    public function canManageTeamMembersFor(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin can manage all teams
        }

        return $this->isTeamAdminFor($team);
    }

    /**
     * Check if user can access team content (projects, tasks, comments)
     */
    public function canAccessTeamContent(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin has access to everything
        }

        return $this->hasRoleInTeam($team);
    }

    /**
     * Get all teams where user has a specific permission
     */
    public function getTeamsWithPermission(string $permission): \Illuminate\Database\Eloquent\Collection
    {
        $permissionMap = [
            'wellness_access' => $this->wellnessAccessTeams,
            'manage_members' => $this->adminTeams,
            'rh_access' => $this->rhTeams,
        ];

        return $permissionMap[$permission] ?? collect();
    }

    // =====================================
    // PERMISSIONS DE SUPPRESSION
    // =====================================

    /**
     * Check if user can delete a team
     */
    public function canDeleteTeam(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin can delete any team
        }

        return $this->isTeamAdminFor($team);
    }

    /**
     * Check if user can delete a project
     */
    public function canDeleteProject(\App\Models\Project $project): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin can delete any project
        }

        return $this->isTeamAdminFor($project->team);
    }

    /**
     * Check if user can delete a task
     */
    public function canDeleteTask(\App\Models\Task $task): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin can delete any task
        }

        return $this->isTeamAdminFor($task->project->team);
    }

    /**
     * Check if user can delete a comment
     */
    public function canDeleteComment(\App\Models\Comment $comment): bool
    {
        // Si l'utilisateur est l'auteur du commentaire, il peut le supprimer
        if ($comment->user_id === $this->id) {
            return true;
        }

        // Site admin peut supprimer n'importe quel commentaire
        if ($this->isAdmin()) {
            return true;
        }

        // Team admin peut supprimer les commentaires dans son équipe
        $team = null;
        if ($comment->isTaskComment()) {
            $team = $comment->task->project->team;
        } elseif ($comment->isProjectComment()) {
            $team = $comment->project->team;
        }

        return $team ? $this->isTeamAdminFor($team) : false;
    }
}
