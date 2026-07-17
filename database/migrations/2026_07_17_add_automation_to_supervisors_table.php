<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->boolean('auto_deadline_reminder')->default(false);
            $table->json('reminder_days_before')->nullable();
            $table->boolean('auto_submission_reminder')->default(false);
            $table->integer('submission_reminder_days')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropColumn(['auto_deadline_reminder', 'reminder_days_before', 'auto_submission_reminder', 'submission_reminder_days']);
        });
    }
};
