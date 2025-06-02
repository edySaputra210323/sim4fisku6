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
        Schema::create('posisi_kepegawaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('restrict');
            $table->foreignId('unit_id')->constrained('unit')->onDelete('restrict');
            $table->enum('status', ['permanent', 'contract', 'honorary'])->comment('Status Kepegawaian');
            $table->string('no_sk_pengangkatan')->nullable()->comment('Nomor SK Pengangkatan');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posisi_kepegawaian');
    }
};
