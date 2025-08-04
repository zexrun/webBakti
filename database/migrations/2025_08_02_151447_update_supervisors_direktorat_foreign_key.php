<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('supervisors', function (Blueprint $table) {
            // Ubah kolom direktorat jadi nullable
            $table->string('direktorat')->nullable()->change();
        });

        // Tambahkan foreign key setelah kolom nullable
        Schema::table('supervisors', function (Blueprint $table) {
            $table->foreign('direktorat')
                  ->references('name')
                  ->on('directorates')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        // Hapus foreign key jika rollback
        Schema::table('supervisors', function (Blueprint $table) {
            $table->dropForeign(['direktorat']);
        });

        // Ubah kembali jadi NOT NULL jika sebelumnya tidak nullable
        Schema::table('supervisors', function (Blueprint $table) {
            $table->string('direktorat')->nullable(false)->change();
        });
    }
};
