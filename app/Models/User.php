<?php

namespace App\Models;

use Devdojo\Auth\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Devdojo\Auth\Models\User as AuthUser;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends AuthUser
{
    public function projects()
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }

    public function Teams()
    {
        return $this->belongsToMany(Team::class, 'team_users');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    use HasFactory;
}

