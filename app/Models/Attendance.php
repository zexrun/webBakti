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

    public function getIsLateAttribute()
    {
        if (!$this->check_in) return false;
        
        $settings = AttendanceSetting::getSettings();
        $workStart = Carbon::parse($settings->work_start_time);
        $checkIn = Carbon::parse($this->check_in->format('H:i:s'));
        
        return $checkIn->gt($workStart->addMinutes($settings->late_tolerance_minutes));
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'present' => 'bg-green-100 text-green-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'absent' => 'bg-red-100 text-red-800',
            'pending' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }
}
