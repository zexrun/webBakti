<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 1 User Admin
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'role' => 'admin', // Asumsi ada kolom role
            'password' => Hash::make('password'),
        ]);

        // Membuat 1 User Mahasiswa
        User::create([
            'name' => 'Mahasiswa Contoh',
            'email' => 'mahasiswa@example.com',
            'role' => 'student', // Asumsi ada kolom role
            'password' => Hash::make('password'),
        ]);

        User::factory(20)->create([
            'role' => 'student',
        ]); // Membuat 20 User lainnya dengan factory

        User::factory(5)->create([
            'role' => 'supervisor',
        ]);
    }
}