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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->nullOnDelete();
            $table->string('nm_pengirim', 255)->nullable();
            $table->date('tgl_terima')->nullable();
            $table->string('no_surat', 50)->nullable()->unique();
            $table->date('tgl_surat')->nullable();
            $table->string('perihal', 255)->required();
            $table->string('asal_surat', 255)->nullable();
            $table->string('file_surat', 255)->nullable();
            $table->enum('status', ['diterima', 'diproses', 'selesai'])->default('diterima');
            $table->foreignId('created_by')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->foreign_id('updated_by')->nullable()->constrained('pegawai')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
