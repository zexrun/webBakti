<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($attendance) {
            if ($attendance->check_in_photo && Storage::disk('public')->exists($attendance->check_in_photo)) {
                Storage::disk('public')->delete($attendance->check_in_photo);
            }
            if ($attendance->check_out_photo && Storage::disk('public')->exists($attendance->check_out_photo)) {
                Storage::disk('public')->delete($attendance->check_out_photo);
            }
        });
    }

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'check_in_photo',
        'check_out_photo',
        'notes',
        'status',
        'supervisor_approval',
        'supervisor_notes',
        'approved_by',
        'approved_at',
        'requires_manual_review',
        'location_verification_status',
        'location_spoofing_score',
        'location_verification_details',
        'location_notes',
        'face_verification_status',
        'face_match_distance',
        'check_out_face_verification_status',
        'check_out_face_match_distance',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'approved_at' => 'datetime',
        'check_in_latitude' => 'decimal:8',
        'check_in_longitude' => 'decimal:8',
        'check_out_latitude' => 'decimal:8',
        'check_out_longitude' => 'decimal:8',
        'location_verification_details' => 'array',
        'requires_manual_review' => 'boolean',
    ];

    protected $appends = [
        'check_in_photo_url',
        'check_out_photo_url',
        'working_hours',
        'is_late',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getWorkingHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        return $this->check_in->diffInHours($this->check_out);
    }

    public function getCheckInPhotoUrlAttribute()
    {
        return $this->check_in_photo ? Storage::disk('public')->url($this->check_in_photo) : null;
    }

    public function getCheckOutPhotoUrlAttribute()
    {
        return $this->check_out_photo ? Storage::disk('public')->url($this->check_out_photo) : null;
    }

    public function getIsLateAttribute()
    {
        if (!$this->check_in) return false;
        
        $settings = AttendanceSetting::getSettings();
        $workStart = Carbon::parse($settings->work_start_time);
        $checkIn = Carbon::parse($this->check_in->format('H:i:s'));
        
        return $checkIn->gt($workStart->addMinutes($settings->late_tolerance_minutes));
    }
}
