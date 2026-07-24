<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('university')->nullable()->after('universitas');
            $table->string('study_program')->nullable()->after('program_studi');
            $table->date('period_start')->nullable()->after('periode_mulai');
            $table->date('period_end')->nullable()->after('periode_selesai');
            $table->string('directorate')->nullable()->after('direktorat');
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->after('nip');
            $table->string('position')->nullable()->after('jabatan');
            $table->string('directorate')->nullable()->after('direktorat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['university', 'study_program', 'period_start', 'period_end', 'directorate']);
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'position', 'directorate']);
        });
    }
};
