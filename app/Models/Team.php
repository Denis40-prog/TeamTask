<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function Users()
    {
        return $this->belongsToMany(User::class, 'team_users');
    }

    use HasFactory;
}
