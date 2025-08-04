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
            'name' => 'Rifqy',
            'username' => 'zexrun',
            'email' => 'rifqykhuzaini@gmail.com',
            'password' => Hash::make('1'), // Ganti dengan password yang sesuai
        ]);

        User::factory()->create([
            'role' => 'supervisor',
            'name' => 'Caca',
            'username' => 'chaoclat',
            'email' => 'caca@gmail.com',
            'password' => Hash::make('1'), // Ganti dengan password yang sesuai
        ]);
        
        User::factory()->create([
            'role' => 'supervisor',
            'name' => 'Charissa',
            'username' => 'cacaw',
            'email' => 'cacaw@gmail.com',
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
            'email' => 'hiro@gmail.com',
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
            'name' => 'Rifqy',
            'username' => 'iki',
            'email' => 'mrifqy821@gmail.com',
            'password' => Hash::make('1'),
        ]);
    }
}