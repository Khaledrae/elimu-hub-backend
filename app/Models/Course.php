<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'teacher_id',
        'thumbnail',
        'level',
        'status',
    ];

    protected static function booted()
    {
        static::creating(function ($course) {
            $course->slug = $course->slug ?? Str::slug($course->title);
        });
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id')->with('user');
    }

    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_course', 'course_id', 'class_id');
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
