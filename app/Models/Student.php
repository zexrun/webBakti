<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'supervisor_id',
        'nim',
        'universitas',
    ];

    /**
     * Mendefinisikan relasi one-to-one ke model User.
     * Satu data student dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi one-to-one ke model Supervisor.
     * Satu student dibimbing oleh satu supervisor.
     */
    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }
}
