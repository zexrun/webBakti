<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Rifqy',
            'email' => 'mrifqy821@gmail.com',
            'username' => 'rifqy',
            'role' => 'student',
            'password' => Hash::make('1'),
        ]);
        User::factory()->create([
            'name' => 'Devin',
            'email' => 'test1@email.com',
            'username' => 'devin',
            'role' => 'student',
            'password' => Hash::make('1'),
        ]);
        User::factory()->create([
            'name' => 'Nibras',
            'email' => 'test2@email.com',
            'username' => 'nibras',
            'role' => 'student',
            'password' => Hash::make('1'),
        ]);
    }
}