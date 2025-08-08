<?php

namespace Database\Seeders; // Tambahkan namespace di sini

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 1 user Admin secara spesifik
        User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@bakti.com',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('1'),
        ]);
    }
}