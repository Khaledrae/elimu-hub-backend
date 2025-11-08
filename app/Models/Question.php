<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'set_by',
        'question_text',
        'marks',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
    ];

    // Relationships
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'set_by', 'user_id')->with('user');
    }
}

