<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WellnessSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'date', 'sleep', 'stress', 'soreness', 'energy',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
