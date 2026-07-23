<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            // Storage path on the 'public' disk, mirroring attendances.check_in_photo

            $table->json('face_descriptor')->nullable();
            // 128-number face-api.js descriptor computed client-side from profile_photo
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'face_descriptor']);
        });
    }
};
