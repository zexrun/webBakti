<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_start_time',
        'work_end_time',
        'late_tolerance_minutes',
        'location_radius_meters',
        'require_photo',
        'require_location',
        'office_latitude',
        'office_longitude',
        'office_address',
    ];

    protected $casts = [
        'require_photo' => 'boolean',
        'require_location' => 'boolean',
        'office_latitude' => 'decimal:8',
        'office_longitude' => 'decimal:8',
        'late_tolerance_minutes' => 'integer',
        'location_radius_meters' => 'integer',
    ];

    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'work_start_time' => '08:00:00',
                'work_end_time' => '17:00:00',
                'late_tolerance_minutes' => 15,
                'location_radius_meters' => 100,
                'require_photo' => true,
                'require_location' => true,
            ]);
        }
        
        return $settings;
    }
}
