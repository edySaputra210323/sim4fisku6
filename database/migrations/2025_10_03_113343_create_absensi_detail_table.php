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
        Schema::create('absensi_detail', function (Blueprint $table) {
                $table->id();
                $table->foreignId('absensi_header_id')
                      ->constrained('absensi_header')
                      ->cascadeOnDelete();
    
                $table->foreignId('riwayat_kelas_id')
                      ->constrained('riwayat_kelas')
                      ->cascadeOnDelete();
    
                $table->enum('status', ['hadir','sakit','izin','alpa'])->default('hadir');
                $table->text('keterangan')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_detail');
    }
};
