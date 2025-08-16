<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Traits\HasTeamPermissions;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasTeamPermissions;
    /**
     * The available user roles.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if the user is a regular user.
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Get teams where user is admin
     */
    public function adminTeams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps()
            ->wherePivot('role', TeamUser::TEAM_ROLE_ADMIN);
    }

    /**
     * Get teams where user is RH
     */
    public function rhTeams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps()
            ->wherePivot('role', TeamUser::TEAM_ROLE_RH);
    }

    /**
     * Get teams where user can access wellness surveys (admin or RH)
     */
    public function wellnessAccessTeams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps()
            ->whereIn('team_user.role', [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH]);
    }

    /**
     * Check if user can access wellness survey for a specific team
     */
    public function canAccessWellnessForTeam(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin has access to everything
        }

        $teamUser = $this->teams()->where('teams.id', $team->id)->first();
        return $teamUser && in_array($teamUser->pivot->role, [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH]);
    }

    /**
     * Check if user can manage members of a specific team
     */
    public function canManageTeamMembers(Team $team): bool
    {
        if ($this->isAdmin()) {
            return true; // Site admin can manage all teams
        }

        $teamUser = $this->teams()->where('teams.id', $team->id)->first();
        return $teamUser && $teamUser->pivot->role === TeamUser::TEAM_ROLE_ADMIN;
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_id');
    }

    public function assignedTasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function xp()
    {
        return $this->hasOne(UserXp::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function challenges()
    {
        return $this->belongsToMany(Challenge::class, 'challenge_participant')->withPivot('progress')->withTimestamps();
    }

    public function wellnessSurveys()
    {
        return $this->hasMany(WellnessSurvey::class);
    }


    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }
}
