<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'project_id',
        'assigned_id',
        'status',
        'priority',
        'due_date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function assigned_user()
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }
}
