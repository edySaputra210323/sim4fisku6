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
        Schema::table('data_siswa', function (Blueprint $table) {
             // Index untuk pencarian nama siswa
             $table->index('nama_siswa');

             // Index untuk filter angkatan
             $table->index('angkatan');
 
             // Index untuk status
             $table->index('status_id');
 
             // Index untuk unit
             $table->index('unit_id');
 
             // Optional: composite index kalau sering dipakai barengan
             $table->index(['angkatan', 'status_id']);
             $table->index(['unit_id', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_siswa', function (Blueprint $table) {
            $table->dropIndex(['nama_siswa']);
            $table->dropIndex(['angkatan']);
            $table->dropIndex(['status_id']);
            $table->dropIndex(['unit_id']);
            $table->dropIndex(['angkatan', 'status_id']);
            $table->dropIndex(['unit_id', 'status_id']);
        });
    }
};
