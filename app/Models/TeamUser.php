<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    use HasFactory;

    /**
     * The available team roles.
     */
    public const TEAM_ROLE_USER = 'user';
    public const TEAM_ROLE_ADMIN = 'admin';
    public const TEAM_ROLE_RH = 'rh';

    protected $table = 'team_user';

    protected $fillable = ['user_id', 'team_id', 'role'];

    /**
     * Check if the team user is a team admin.
     */
    public function isTeamAdmin(): bool
    {
        return $this->role === self::TEAM_ROLE_ADMIN;
    }

    /**
     * Check if the team user is RH.
     */
    public function isTeamRH(): bool
    {
        return $this->role === self::TEAM_ROLE_RH;
    }

    /**
     * Check if the team user is a regular user.
     */
    public function isTeamUser(): bool
    {
        return $this->role === self::TEAM_ROLE_USER;
    }

    /**
     * Check if the team user can access wellness survey (admin or RH).
     */
    public function canAccessWellnessSurvey(): bool
    {
        return in_array($this->role, [self::TEAM_ROLE_ADMIN, self::TEAM_ROLE_RH]);
    }

    /**
     * Check if the team user can manage team members (only admin).
     */
    public function canManageTeamMembers(): bool
    {
        return $this->role === self::TEAM_ROLE_ADMIN;
    }

    /**
     * Get the user relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team relationship
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
