<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenUser extends Model
{
    protected $connection = 'absensi_mysql';
    protected $table = 'users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    public $timestamps = false;
}
