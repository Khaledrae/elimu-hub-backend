<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'teacher_id',
        'title',
        'instructions',
        'type',
        'total_marks',
        'duration_minutes',
        'status',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id')->with('user');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
