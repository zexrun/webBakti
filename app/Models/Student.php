<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supervisor_id',
        'nim',
        'universitas',
        'program_studi',
        'semester',
        'periode_mulai',
        'periode_selesai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function tasks() 
    {
        return $this->belongsToMany(Task::class, 'task_student');
    }
        
    public function submissions() 
    {
        return $this->hasMany(Submission::class);
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class)->orderBy('activity_date', 'desc');
    }

}
