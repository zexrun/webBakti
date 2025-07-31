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
        Schema::table('final_assessments', function (Blueprint $table) {
            $table->timestamp('certificate_generated_at')->nullable()->after('overall_comments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_assessments', function (Blueprint $table) {
            //
        });
    }
};
