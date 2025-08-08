<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
    // Laravel otomatis pakai tabel "directorates"

    protected $fillable = ['name']; // agar bisa mass assignment seperti create()
}
