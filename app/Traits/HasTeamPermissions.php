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
}
