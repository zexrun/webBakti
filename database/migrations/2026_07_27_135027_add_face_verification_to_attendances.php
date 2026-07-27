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
        Schema::table('attendances', function (Blueprint $table) {
            $table->float('face_distance')->nullable()->comment('Face descriptor distance during verification');
            $table->float('location_distance')->nullable()->comment('Distance from office in meters');
            $table->enum('verification_status', ['auto_verified', 'manual_verified', 'rejected', 'pending'])
                ->default('pending')
                ->comment('Verification status');
            $table->timestamp('verified_at')->nullable()->comment('When verification was completed');
            $table->text('verification_notes')->nullable()->comment('Notes from verification');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('face_distance');
            $table->dropColumn('location_distance');
            $table->dropColumn('verification_status');
            $table->dropColumn('verified_at');
            $table->dropColumn('verification_notes');
        });
    }
};
