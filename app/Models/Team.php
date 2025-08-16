<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Get team admins
     */
    public function admins()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->wherePivot('role', TeamUser::TEAM_ROLE_ADMIN);
    }

    /**
     * Get team RH users
     */
    public function rhUsers()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->wherePivot('role', TeamUser::TEAM_ROLE_RH);
    }

    /**
     * Get users who can access wellness surveys (admin or RH)
     */
    public function wellnessAccessUsers()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps()
            ->whereIn('team_user.role', [TeamUser::TEAM_ROLE_ADMIN, TeamUser::TEAM_ROLE_RH]);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getHexagonRouteAttribute()
    {
        $projectCount = $this->projects()->count();

        if ($projectCount === 1) {
            return route('projects.show', $this->projects()->first());
        }

        return route('livewire.projects', ['teamId' => $this->id]);
    }
}
