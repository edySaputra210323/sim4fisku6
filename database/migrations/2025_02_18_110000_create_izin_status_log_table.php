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
        Schema::create('izin_status_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('izin_pegawai_id')->constrained('izin_pegawai')->onDelete('cascade');
            $table->string('dari_status')->nullable();
            $table->string('ke_status');
            // user yang melakukan perubahan (pegawai/ks/sdm)
            $table->foreignId('diubah_by')->constrained('users')->onDelete('cascade');

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_status_log');
    }
};
