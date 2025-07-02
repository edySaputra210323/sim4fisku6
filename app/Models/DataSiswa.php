<?php

namespace App\Models;

use App\Models\User;
use App\Models\Transport;
use App\Models\NilaiSiswa;
use App\Models\JarakTempuh;
use App\Models\MutasiSiswa;
use App\Models\StatusSiswa;
use App\Models\RiwayatKelas;
use App\Models\PekerjaanOrtu;
use App\Models\PendidikanOrtu;
use App\Models\PenghasilanOrtu;
use App\Models\PrestasiPelanggaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSiswa extends Model
{
    use SoftDeletes;
    protected $table = 'data_siswa';
    protected $fillable = [
        'nama_siswa',
        'nis',
        'nisn',
        'nik',
        'virtual_account',
        'no_hp',
        'email',
        'agama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'negara',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'alamat',
        'rt',
        'rw',
        'kode_pos',
        'yatim_piatu',
        'penyakit',
        'jumlah_saudara',
        'anak_ke',
        'dari_bersaudara',
        'jarak_tempuh_id',
        'transport_id',
        'angkatan',
        'tanggal_masuk',
        'tanggal_keluar',
        'lanjut_sma_dimana',
        'status_id',
        'pindah_id',
        'upload_ijazah_sd',
        'foto_siswa',
        'nm_ayah',
        'pendidikan_ayah_id',
        'pekerjaan_ayah_id',
        'penghasilan_ayah_id',
        'no_hp_ayah',
        'nm_ibu',
        'pendidikan_ibu_id',
        'pekerjaan_ibu_id',
        'penghasilan_ibu_id',
        'no_hp_ibu',
        'nm_wali',
        'pendidikan_wali_id',
        'pekerjaan_wali_id',
        'penghasilan_wali_id',
        'no_hp_wali',
        'user_id',
        'unit_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'jenis_kelamin' => 'string',
        'yatim_piatu' => 'string',
    ];

    // Relasi ke JarakTempuh
    public function jarakTempuh()
    {
        return $this->belongsTo(JarakTempuh::class, 'jarak_tempuh_id');
    }

    // Relasi ke Transport
    public function transport()
    {
        return $this->belongsTo(Transport::class, 'transport_id');
    }

    // Relasi ke StatusSiswa
    public function status()
    {
        return $this->belongsTo(StatusSiswa::class, 'status_id');
    }

    // Relasi ke MutasiSiswa (pindah)
    public function pindah()
    {
        return $this->belongsTo(MutasiSiswa::class, 'pindah_id');
    }

    // Relasi ke PendidikanOrtu (ayah)
    public function pendidikanAyah()
    {
        return $this->belongsTo(PendidikanOrtu::class, 'pendidikan_ayah_id');
    }

    // Relasi ke PekerjaanOrtu (ayah)
    public function pekerjaanAyah()
    {
        return $this->belongsTo(PekerjaanOrtu::class, 'pekerjaan_ayah_id');
    }

    // Relasi ke PenghasilanOrtu (ayah)
    public function penghasilanAyah()
    {
        return $this->belongsTo(PenghasilanOrtu::class, 'penghasilan_ayah_id');
    }

    // Relasi ke PendidikanOrtu (ibu)
    public function pendidikanIbu()
    {
        return $this->belongsTo(PendidikanOrtu::class, 'pendidikan_ibu_id');
    }

    // Relasi ke PekerjaanOrtu (ibu)
    public function pekerjaanIbu()
    {
        return $this->belongsTo(PekerjaanOrtu::class, 'pekerjaan_ibu_id');
    }

    // Relasi ke PenghasilanOrtu (ibu)
    public function penghasilanIbu()
    {
        return $this->belongsTo(PenghasilanOrtu::class, 'penghasilan_ibu_id');
    }

    // Relasi ke PendidikanOrtu (wali)
    public function pendidikanWali()
    {
        return $this->belongsTo(PendidikanOrtu::class, 'pendidikan_wali_id');
    }

    // Relasi ke PekerjaanOrtu (wali)
    public function pekerjaanWali()
    {
        return $this->belongsTo(PekerjaanOrtu::class, 'pekerjaan_wali_id');
    }

    // Relasi ke PenghasilanOrtu (wali)
    public function penghasilanWali()
    {
        return $this->belongsTo(PenghasilanOrtu::class, 'penghasilan_wali_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke MutasiSiswa
    public function mutasiSiswa()
    {
        return $this->hasMany(MutasiSiswa::class, 'data_siswa_id');
    }

    // Relasi ke RiwayatKelas
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'data_siswa_id');
    }

    // Relasi ke PrestasiPelanggaran
    public function prestasiPelanggaran()
    {
        return $this->hasMany(PrestasiPelanggaran::class, 'data_siswa_id');
    }

    // Relasi ke NilaiSiswa
    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class, 'data_siswa_id');
    }
}
    
