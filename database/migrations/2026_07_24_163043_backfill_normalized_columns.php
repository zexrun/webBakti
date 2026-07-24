<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('UPDATE students SET university = universitas WHERE university IS NULL');
        DB::statement('UPDATE students SET study_program = program_studi WHERE study_program IS NULL');
        DB::statement('UPDATE students SET period_start = periode_mulai WHERE period_start IS NULL');
        DB::statement('UPDATE students SET period_end = periode_selesai WHERE period_end IS NULL');
        DB::statement('UPDATE students SET directorate = direktorat WHERE directorate IS NULL');

        DB::statement('UPDATE supervisors SET employee_id = nip WHERE employee_id IS NULL');
        DB::statement('UPDATE supervisors SET position = jabatan WHERE position IS NULL');
        DB::statement('UPDATE supervisors SET directorate = direktorat WHERE directorate IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set new columns back to NULL
        DB::statement('UPDATE students SET university = NULL');
        DB::statement('UPDATE students SET study_program = NULL');
        DB::statement('UPDATE students SET period_start = NULL');
        DB::statement('UPDATE students SET period_end = NULL');
        DB::statement('UPDATE students SET directorate = NULL');

        DB::statement('UPDATE supervisors SET employee_id = NULL');
        DB::statement('UPDATE supervisors SET position = NULL');
        DB::statement('UPDATE supervisors SET directorate = NULL');
    }
};
