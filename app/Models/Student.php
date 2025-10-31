<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    //
     use HasFactory;
    protected $fillable = [
        'user_id',
        'admission_number',
        'grade_level',
        'school_name',
        'dob',
        'gender',
        'guardian_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_id');
    }
}
