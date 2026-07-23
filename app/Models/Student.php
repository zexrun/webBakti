<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'direktorat',
        'periode_mulai',
        'periode_selesai',
        'profile_photo',
        'face_descriptor',
    ];

    protected $casts = [
        'face_descriptor' => 'array',
    ];

    protected $appends = [
        'profile_photo_url',
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

    public function logbooks()
    {
        return $this->hasMany(Logbook::class)->orderBy('activity_date', 'desc');
    }

    public function finalAssessment()
    {
        return $this->hasOne(FinalAssessment::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class);
    }

    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, User::class, 'id', 'user_id');
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? Storage::disk('public')->url($this->profile_photo) : null;
    }
}
