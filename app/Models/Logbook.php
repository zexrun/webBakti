<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logbook extends Model
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
        'file_path',
        'is_verified',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
