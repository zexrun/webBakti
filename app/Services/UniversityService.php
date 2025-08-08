<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UniversityService
{
    /**
     * Mengambil data universitas dari API atau database
     */

    /**
     * Mengambil data universitas dari database
     */
    private function getUniversitiesFromDatabase()
    {
        // Coba dari tabel universities
        if (\Schema::hasTable('universities')) {
            return \App\Models\University::active()
                ->orderBy('name')
                ->pluck('name')
                ->toArray();
        }

        // Fallback ke data dari students
        return \App\Models\Student::whereNotNull('universities')
            ->distinct()
            ->orderBy('universities')
            ->pluck('universities')
            ->toArray();
    }

    /**
     * Menambah universitas baru jika belum ada
     */
    public function addUniversityIfNotExists($universityName)
    {
        if (\Schema::hasTable('universities')) {
            \App\Models\University::firstOrCreate(
                ['name' => $universityName],
                [
                    'type' => 'unknown',
                    'is_active' => true
                ]
            );
        }
    }
}
