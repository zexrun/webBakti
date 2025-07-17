<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{

    use HasFactory;
    
    protected $fillable = [
        'student_id',
        'title',
        'activity_date',
        'start_time',
        'end_time',
        'description',
        'feeling',
        'photo_path',
        'is_verified',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
}
