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
        User::factory()->create([
            'role' => 'admin',
            'name' => 'Admin Bakti',
            'username' => 'Admin',
            'email' => 'rifqykhuzaini@gmail.com',
            'password' => Hash::make('1'), // Ganti dengan password yang sesuai
        ]);

        User::factory()->create([
            'role' => 'supervisor',
            'name' => 'SPV 1',
            'username' => 'spv1',
            'email' => 'spv1@testemail.com',
            'password' => Hash::make('1'), // Ganti dengan password yang sesuai
        ]);
        
        User::factory()->create([
            'role' => 'supervisor',
            'name' => 'SPV 2',
            'username' => 'spv2',
            'email' => 'spv2@testemail.com',
            'password' => Hash::make('1'), // Ganti dengan password yang sesuai
        ]);

        User::factory()->create([
            'role' => 'student',
            'name' => 'Keiko',
            'username' => 'keiko',
            'email' => 'keiko@gmail.com',
            'password' => Hash::make('1'), // Ganti dengan password yang sesuai
        ]);
        
        User::factory()->create([
            'role' => 'student',
            'name' => 'Hiro',
            'username' => 'hiro',
            'email' => 'zexrun2005@gmail.com',
            'password' => Hash::make('1'),
        ]);
        User::factory()->create([
            'role' => 'student',
            'name' => 'Kimi',
            'username' => 'kimi',
            'email' => 'kimi@gmail.com',
            'password' => Hash::make('1'),
        ]);
        User::factory()->create([
            'role' => 'student',
            'name' => 'Boots',
            'username' => 'boots',
            'email' => 'mrifqy821@gmail.com',
            'password' => Hash::make('1'),
        ]);
    }
}
