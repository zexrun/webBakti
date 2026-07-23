<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('face_verification_status')->nullable();
            // verified, mismatch, no_reference, unavailable - null on rows from before this feature

            $table->float('face_match_distance')->nullable();
            // Euclidean distance between check-in and reference descriptors, when both exist
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['face_verification_status', 'face_match_distance']);
        });
    }
};
