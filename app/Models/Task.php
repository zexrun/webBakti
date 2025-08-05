<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    
    protected $fillable = ['supervisor_id', 'title', 'description', 'file_path', 'type', 'due_date'];

    public function supervisor() {
        return $this->belongsTo(Supervisor::class);
    }
    public function students() {
        return $this->belongsToMany(Student::class, 'task_student');
    }
    public function submissions() {
        return $this->hasMany(Submission::class);
    }



}
