<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_number',
        'subject_specialization',
        'school_name',
        'qualification',
        'experience_years',
        'gender',
        'dob',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function school()
    {
        return $this->belongsTo(School::class, 'school_name', 'name');
    }
    public function courses()
    {
        return $this->hasMany(Course::class, 'teacher_id', 'user_id');
    }
}
