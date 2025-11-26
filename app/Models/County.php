<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{
    protected $fillable = ['name', 'code'];

    public function Users()
    {
        return $this->hasMany(User::class, 'county', 'id');
    }
    public function county()
    {
        return $this->hasMany(School::class, 'county', 'id');
    }
}

