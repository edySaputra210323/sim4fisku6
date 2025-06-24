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
        Schema::create('data_siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_siswa', 100);
            $table->string('nis', 20);
            $table->string('nisn', 20);
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('agama', 50)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('negara', 50)->nullable();
            $table->string('provinsi', 50)->nullable();
            $table->string('kabupaten', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('kelurahan', 50)->nullable();
            $table->string('alamat', 100)->nullable();
            $table->string('rt', 50)->nullable();
            $table->string('rw', 50)->nullable();
            $table->string('kode_pos', 50)->nullable();
            $table->enum('yatim_piatu', ['Ya', 'Tidak'])->nullable();
            $table->string('penyakit', 100)->nullable();
            $table->string('jumlah_saudara', 100)->nullable();
            $table->string('anak_ke', 100)->nullable();
            $table->string('dari_bersaudara', 100)->nullable();
            $table->foreignId('jarak_tempuh_id')->constrained('jarak_tempuh')->nullOnDelete();
            $table->foreignId('transport_id')->constrained('transport')->nullOnDelete();
            $table->string('angkatan', 50);
            $table->date('tanggal_masuk');
            $table->string('foto_siswa', 100)->nullable();
            $table->foreignId('status_siswa_id')->constrained('status_siswa')->nullOnDelete();
            $table->string('nm_ayah', 100)->nullable();
            $table->foreignId('pendidikan_ayah_id')->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_ayah_id')->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_ayah_id')->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_ayah', 20)->nullable();
            $table->string('nm_ibu', 100)->nullable();
            $table->foreignId('pendidikan_ibu_id')->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_ibu_id')->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_ibu_id')->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_ibu', 20)->nullable();
            $table->string('nm_wali', 100)->nullable();
            $table->foreignId('pendidikan_wali_id')->constrained('pendidikan_ortu')->nullOnDelete();
            $table->foreignId('pekerjaan_wali_id')->constrained('pekerjaan_ortu')->nullOnDelete();
            $table->foreignId('penghasilan_wali_id')->constrained('penghasilan_ortu')->nullOnDelete();
            $table->string('no_hp_wali', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_siswa');
    }
};
