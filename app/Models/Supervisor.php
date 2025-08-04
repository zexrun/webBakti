<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
        'direktorat'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function logbook()
    {
        return $this->hasMany(Logbook::class)->orderBy('activity_date', 'desc');
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }
    
    public function position()
    {
        return $this->belongsTo(Position::class, 'jabatan', 'name');
    }
}
