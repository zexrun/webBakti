<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable strict mode temporarily to avoid truncation warnings
        DB::statement("SET sql_mode=''");

        // Step 1: Temporarily change enum to VARCHAR to allow both old and new values
        DB::statement("ALTER TABLE tasks MODIFY type VARCHAR(255)");

        // Step 2: Update data to new values
        DB::statement("UPDATE tasks SET type = 'daily' WHERE type = 'harian'");
        DB::statement("UPDATE tasks SET type = 'final' WHERE type = 'akhir'");

        // Step 3: Change back to enum with new values
        DB::statement("ALTER TABLE tasks MODIFY type ENUM('daily', 'final') DEFAULT 'daily'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable strict mode temporarily to avoid truncation warnings
        DB::statement("SET sql_mode=''");

        // Step 1: Temporarily change enum to VARCHAR to allow both old and new values
        DB::statement("ALTER TABLE tasks MODIFY type VARCHAR(255)");

        // Step 2: Convert data back to old values
        DB::statement("UPDATE tasks SET type = 'harian' WHERE type = 'daily'");
        DB::statement("UPDATE tasks SET type = 'akhir' WHERE type = 'final'");

        // Step 3: Revert the enum column
        DB::statement("ALTER TABLE tasks MODIFY type ENUM('harian', 'akhir') DEFAULT 'harian'");
    }
};
