<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    protected $fillable = [
        'student_id',
        'assessment_id',
        'started_at',
        'attempt_number',
        'submitted_at',
        'total_marks_scored',
        'total_marks_possible',
        'score_percentage',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score_percentage' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function responses()
    {
        return $this->hasMany(StudentResponse::class);
    }
}
