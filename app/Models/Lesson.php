<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable = [
        'course_id',
        'class_id',
        'teacher_id',
        'title',
        'content',
        'video_url',
        'document_path',
        'order',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'user_id');
    }
}
