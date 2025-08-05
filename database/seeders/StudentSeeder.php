<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 20 user Mahasiswa secara acak
        User::factory(20)->create([
            'role' => 'student',
        ]);
    }
}