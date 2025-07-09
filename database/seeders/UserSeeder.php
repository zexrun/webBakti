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
            'email' => 'rifqykhuzaini@gmail.com',
            'password' => Hash::make('12341234'), // Ganti dengan password yang sesuai
        ]);

        User::factory()->create([
            'role' => 'supervisor',
            'name' => 'Caca',
            'email' => 'caca@gmail.com',
            'password' => Hash::make('12341234'), // Ganti dengan password yang sesuai
        ]);

        User::factory()->create([
            'role' => 'student',
            'name' => 'Keiko',
            'email' => 'keiko@gmail.com',
            'password' => Hash::make('12341234'),
        ]);
        
        User::factory(20)->create([
            'role' => 'student',
        ]); // Membuat 20 User lainnya dengan factory

        User::factory(5)->create([
            'role' => 'supervisor',
        ]);
    }
}