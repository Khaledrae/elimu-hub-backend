<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'level_group', 'manager_id'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'class_course', 'class_id', 'course_id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'class_id');
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
