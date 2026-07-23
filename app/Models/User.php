<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'activation_token',
        'email_verified_at',
        'profile_photo',
        'face_descriptor',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * face_descriptor is a 128-float array read only by the backend
     * (AttendanceController::checkIn(), FaceVerificationService) - no
     * frontend page reads it directly, so it's excluded from
     * serialization to avoid bloating every User payload with data
     * nothing renders. profile_photo is superseded by the computed
     * profile_photo_url accessor below, same pattern as
     * Attendance::check_in_photo_url.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'profile_photo',
        'face_descriptor',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'face_descriptor' => 'array',
        ];
    }

    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function getSupervisorOrNull()
    {
        return $this->supervisor()->first();
    }

    public function getStudentOrNull()
    {
        return $this->student()->first();
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo ? Storage::disk('public')->url($this->profile_photo) : null;
    }
}

