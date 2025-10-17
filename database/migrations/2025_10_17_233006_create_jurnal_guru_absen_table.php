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
        Schema::create('jurnal_guru_absen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_guru_id')
                ->constrained('jurnal_guru')
                ->cascadeOnDelete();

            $table->foreignId('riwayat_kelas_id')
                ->constrained('riwayat_kelas')
                ->cascadeOnDelete();

            $table->enum('status', ['sakit', 'izin', 'alpa'])->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_guru_absen');
    }
};
