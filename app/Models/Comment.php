<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'task_id',
        'project_id',
        'user_id',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // S'assurer qu'un commentaire a soit task_id soit project_id mais pas les deux
        static::creating(function ($comment) {
            if (empty($comment->task_id) && empty($comment->project_id)) {
                throw ValidationException::withMessages([
                    'comment' => 'Un commentaire doit être associé soit à une tâche soit à un projet.'
                ]);
            }

            if (!empty($comment->task_id) && !empty($comment->project_id)) {
                throw ValidationException::withMessages([
                    'comment' => 'Un commentaire ne peut pas être associé à la fois à une tâche et à un projet.'
                ]);
            }
        });
    }

    /**
     * Check if this comment belongs to a task
     */
    public function isTaskComment(): bool
    {
        return !empty($this->task_id);
    }

    /**
     * Check if this comment belongs to a project
     */
    public function isProjectComment(): bool
    {
        return !empty($this->project_id);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
