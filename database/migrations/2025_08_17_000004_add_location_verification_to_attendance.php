<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Location verification fields
            $table->string('location_verification_status')->default('unverified');
            // Options: unverified, verified, flagged, suspicious

            $table->integer('location_spoofing_score')->nullable();
            // 0-100, higher = more suspicious

            $table->json('location_verification_details')->nullable();
            // Store full verification result for audit trail

            $table->string('photo_exif_status')->nullable();
            // none, valid, missing, invalid

            $table->boolean('requires_manual_review')->default(false);
            // Flag for admin to review suspicious attendance

            $table->text('location_notes')->nullable();
            // Admin notes on location verification
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'location_verification_status',
                'location_spoofing_score',
                'location_verification_details',
                'photo_exif_status',
                'requires_manual_review',
                'location_notes',
            ]);
        });
    }
};
