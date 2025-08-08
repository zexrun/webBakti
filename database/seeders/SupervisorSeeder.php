<?php

namespace Database\Seeders; // Tambahkan baris ini

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 2 user Supervisor secara spesifik
        User::factory()->create([
            'name' => 'Pak Dede',
            'email' => 'dede@baktitest.com',
            'username' => 'dede',
            'role' => 'supervisor',
            'password' => Hash::make('1'),

        ]);
        User::factory()->create([
            'name' => 'Pak Karma',
            'email' => 'karma@baktitest.com',
            'username' => 'karma',
            'role' => 'supervisor',
            'password' => Hash::make('1'),
        ]);
    }
}