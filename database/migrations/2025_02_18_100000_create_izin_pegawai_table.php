<?php

use App\Enums\StatusPengajuanEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('izin_pegawai', function (Blueprint $table) {
            $table->id();

            $table->foreignId('dibuat_oleh_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jenis_izin_id')->constrained('jenis_izin')->cascadeOnDelete();
            $table->timestamp('jam');
            $table->text('alasan');
            $table->string('status')->default(StatusPengajuanEnum::DRAFT->value);
            $table->string('status_kepala_sekolah')->nullable();
            $table->string('status_sdm')->nullable();
            $table->text('catatan_kepala_sekolah')->nullable();
            $table->text('catatan_sdm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izin_pegawai');
    }
};
