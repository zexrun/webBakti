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
        'feedback',
        'feedback_at',
    ];

    protected function casts(): array
    {
        return [
            'feedback_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
