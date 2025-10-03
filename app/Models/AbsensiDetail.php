<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiDetail extends Model
{
    protected $table = 'absensi_detail';

    protected $fillable = [
        'absensi_header_id',
        'riwayat_kelas_id',
        'status',
        'keterangan',
    ];

    // Relasi ke Header
    public function header()
    {
        return $this->belongsTo(AbsensiHeader::class, 'absensi_header_id');
    }

    // Relasi ke RiwayatKelas
    public function riwayatKelas()
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id');
    }

    // Shortcut relasi ke Data Siswa lewat RiwayatKelas
    public function siswa()
    {
        return $this->hasOneThrough(
            DataSiswa::class, 
            RiwayatKelas::class,
            'id',             // Foreign key di tabel riwayat_kelas
            'id',             // Foreign key di tabel data_siswa
            'riwayat_kelas_id', // Local key di tabel absensi_detail
            'data_siswa_id'     // Local key di tabel riwayat_kelas
        );
    }
}
