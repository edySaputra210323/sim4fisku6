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
        Schema::create('siswa_import_faileds', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa')->nullable();
            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nik')->nullable();
            $table->string('virtual_account')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->string('agama')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('yatim_piatu')->nullable();
            $table->string('penyakit')->nullable();
            $table->string('jumlah_saudara')->nullable();
            $table->string('anak_ke')->nullable();
            $table->string('dari_bersaudara')->nullable();
            $table->foreignId('jarak_tempuh_id')->nullable()->constrained('jarak_tempuh')->nullOnDelete();
            $table->foreignId('transport_id')->nullable()->constrained('transport')->nullOnDelete();
            $table->string('angkatan')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('status_siswa')->nullOnDelete();
            $table->string('nm_ayah')->nullable();
            $table->foreignId('pendidikan_ayah_id')->nullable()->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_ayah_id')->nullable()->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_ayah_id')->nullable()->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_ayah', 15)->nullable();
            $table->string('nm_ibu')->nullable();
            $table->foreignId('pendidikan_ibu_id')->nullable()->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_ibu_id')->nullable()->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_ibu_id')->nullable()->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_ibu', 15)->nullable();
            $table->string('nm_wali')->nullable();
            $table->foreignId('pendidikan_wali_id')->nullable()->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_wali_id')->nullable()->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_wali_id')->nullable()->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_wali', 15)->nullable();
            $table->foreignId('ditambah_oleh')->nullable()->constrained('users');
            $table->foreignId('unit_id')->nullable()->constrained('users');
            $table->text('catatan_gagal')->nullable();
            $table->foreignId('dihapus_oleh')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_import_faileds');
    }
};
