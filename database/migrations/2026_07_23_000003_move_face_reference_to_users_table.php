<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            $table->json('face_descriptor')->nullable();
        });

        // Preserve any reference photo/descriptor a student already saved
        // under the old per-Student scheme before the columns are dropped
        // from students.
        DB::table('students')
            ->whereNotNull('profile_photo')
            ->orWhereNotNull('face_descriptor')
            ->get(['user_id', 'profile_photo', 'face_descriptor'])
            ->each(function ($student) {
                DB::table('users')
                    ->where('id', $student->user_id)
                    ->update([
                        'profile_photo' => $student->profile_photo,
                        'face_descriptor' => $student->face_descriptor,
                    ]);
            });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'face_descriptor']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('profile_photo')->nullable();
            $table->json('face_descriptor')->nullable();
        });

        DB::table('users')
            ->whereNotNull('profile_photo')
            ->orWhereNotNull('face_descriptor')
            ->get(['id', 'profile_photo', 'face_descriptor'])
            ->each(function ($user) {
                DB::table('students')
                    ->where('user_id', $user->id)
                    ->update([
                        'profile_photo' => $user->profile_photo,
                        'face_descriptor' => $user->face_descriptor,
                    ]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_photo', 'face_descriptor']);
        });
    }
};
