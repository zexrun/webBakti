<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
    protected $fillable = ['name'];

    public function supervisors()
    {
        return $this->hasMany(Supervisor::class, 'direktorat', 'name');
    }
}
