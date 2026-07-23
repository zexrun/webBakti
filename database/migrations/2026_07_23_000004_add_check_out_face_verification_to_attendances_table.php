<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('check_out_face_verification_status')->nullable();
            // verified, mismatch, no_reference, unavailable - null until check-out happens

            $table->float('check_out_face_match_distance')->nullable();
            // Euclidean distance between check-out and reference descriptors, when both exist
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['check_out_face_verification_status', 'check_out_face_match_distance']);
        });
    }
};
