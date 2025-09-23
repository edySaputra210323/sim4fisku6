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
        Schema::create('status_pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status'); // contoh: Aktif, Cuti, Mutasi
            $table->string('kode')->unique(); // contoh: aktif, cuti, mutasi
            $table->string('warna')->nullable(); // untuk badge warna di UI
            $table->boolean('is_active')->default(true); // apakah status ini dianggap aktif
            $table->string('keterangan')->nullable(); // untuk keterangan status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pegawai');
    }
};
