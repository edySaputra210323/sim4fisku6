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
        Schema::table('riwayat_kelas', function (Blueprint $table) {
            $table->index('data_siswa_id');
            $table->index('pegawai_id');
            $table->index('tahun_ajaran_id');
            $table->index('semester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riwayat_kelas', function (Blueprint $table) {
            $table->dropIndex(['data_siswa_id']);
            $table->dropIndex(['pegawai_id']);
            $table->dropIndex(['tahun_ajaran_id']);
            $table->dropIndex(['semester_id']);
        });
    }
};
