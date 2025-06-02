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
        Schema::create('training_pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('nm_training')->comment('Nama Pelatihan');
            $table->string('penyelenggara')->comment('Penyelenggara');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration_hours')->nullable()->comment('Durasi dalam jam');
            $table->string('no_sertifikat')->nullable();
            $table->string('file_sertifikat_training')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_pegawai');
    }
};
