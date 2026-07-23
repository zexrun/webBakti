<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'final_grade',
        'overall_comments',
    ];

    protected $casts = [
        'certificate_generated_at' => 'datetime',
    ];

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }
}
