<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Sequence;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat 10 data tugas palsu menggunakan TaskFactory
        Task::factory()
            ->count(10)
            ->state(new Sequence(
                fn (Sequence $sequence) => ['title' => 'Tugas Magang ke-' . $sequence->index + 1],
            ))
            ->create();
    }
}