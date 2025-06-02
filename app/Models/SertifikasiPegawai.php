<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SertifikasiPegawai extends Model
{
    protected $table = 'sertifikasi_pegawai';
    protected $fillable = [
        'pegawai_id',
        'nm_sertifikasi',
        'penerbit',
        'tgl_sertifikasi',
        'tgl_kadaluarsa',
        'no_sertifikat',
        'file_sertifikat_sertifikasi',
    ];

    protected $casts = [
        'tgl_sertifikasi' => 'date',
        'tgl_kadaluarsa' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }  
}
