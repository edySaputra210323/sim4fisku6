<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrestasiPelanggaran extends Model
{
    use SoftDeletes;
    protected $table = 'prestasi_pelanggaran';
    protected $fillable = [
        'data_siswa_id',
        'tahun_ajaran_id',
        'semester_id',
        'tipe',
        'deskripsi',
        'scan_sertifikat_prestasi',
        'scan_sp_pelanggaran',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tipe' => 'string',
    ];

    // Relasi ke DataSiswa
    public function dataSiswa()
    {
        return $this->belongsTo(DataSiswa::class, 'data_siswa_id');
    }

    // Relasi ke TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // Relasi ke Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
