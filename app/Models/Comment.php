<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis via mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'content',
        'task_id',
        'project_id',
        'user_id',
    ];
}
