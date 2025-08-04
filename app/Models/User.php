<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    /**
     * The available user roles.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';
    public const ROLE_MANAGER = 'manager';

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
     * Check if the user is a manager user.
     */
    public function isManager(): bool {
        return $this->role === self::ROLE_MANAGER;
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
        return $this->belongsToMany(Team::class)->withTimestamps();
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
