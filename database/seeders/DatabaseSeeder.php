<?php

namespace Database\Seeders;

use Database\Seeders\AdminSeeder; // Menggunakan namespace yang benar
use Database\Seeders\DirectorateSeeder; // Menggunakan namespace yang benar
use Database\Seeders\PositionSeeder; // Menggunakan namespace yang benar
use Database\Seeders\SupervisorSeeder; // Menggunakan namespace yang benar
use Database\Seeders\StudentSeeder; 
use Database\Seeders\TaskSeeder; 
use Database\Seeders\UniversitySeeder; 

use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            DirectorateSeeder::class,
            PositionSeeder::class,
            SupervisorSeeder::class,
            StudentSeeder::class,
            TaskSeeder::class,
            UniversitySeeder::class,
        ]);
    }
}
