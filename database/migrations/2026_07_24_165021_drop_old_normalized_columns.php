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
        // Drop old columns from students table (already dropped in previous attempt, but keeping for safety)
        Schema::table('students', function (Blueprint $table) {
            $columns = Schema::getColumnListing('students');
            $colsToDrop = [];
            foreach (['universitas', 'program_studi', 'periode_mulai', 'periode_selesai', 'direktorat'] as $col) {
                if (in_array($col, $columns)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });

        // Drop old columns from supervisors table
        Schema::table('supervisors', function (Blueprint $table) {
            // First drop the foreign key constraint if it exists
            $columns = Schema::getColumnListing('supervisors');
            if (in_array('direktorat', $columns)) {
                $table->dropForeign(['direktorat']);
            }
        });

        // Now drop the columns
        Schema::table('supervisors', function (Blueprint $table) {
            $columns = Schema::getColumnListing('supervisors');
            $colsToDrop = [];
            foreach (['nip', 'jabatan', 'direktorat'] as $col) {
                if (in_array($col, $columns)) {
                    $colsToDrop[] = $col;
                }
            }
            if (!empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old columns (reverse of the add migration)
        Schema::table('students', function (Blueprint $table) {
            $table->string('universitas')->nullable();
            $table->string('program_studi')->nullable();
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->string('direktorat')->nullable();
        });

        Schema::table('supervisors', function (Blueprint $table) {
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('direktorat')->nullable();

            // Restore foreign key constraint
            $table->foreign('direktorat')
                  ->references('name')
                  ->on('directorates')
                  ->onDelete('set null');
        });
    }
};
