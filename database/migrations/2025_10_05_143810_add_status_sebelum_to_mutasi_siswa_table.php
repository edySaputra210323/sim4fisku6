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
        Schema::table('mutasi_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('status_sebelum_id')->nullable()->after('tipe_mutasi');
            $table->foreign('status_sebelum_id')->references('id')->on('status_siswa')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mutasi_siswa', function (Blueprint $table) {
            $table->dropForeign(['status_sebelum_id']);
            $table->dropColumn('status_sebelum_id');
        });
    }
};
