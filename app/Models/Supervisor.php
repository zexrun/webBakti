<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'nip',
        'jabatan',
    ];

    /**
     * Mendefinisikan relasi one-to-one ke model User.
     * Satu data supervisor dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi one-to-many ke model Student.
     * Satu supervisor bisa membimbing banyak student.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}