<?php

namespace App\Models;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\AbsensiDetail;
use Illuminate\Database\Eloquent\Model;

class AbsensiHeader extends Model
{
    protected $table = 'absensi_header';

    protected $fillable = [
        'kelas_id',
        'mapel_id',
        'pegawai_id',
        'tahun_ajaran_id',
        'semester_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'pertemuan_ke',
        'kegiatan',
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    // Relasi ke Guru/Pegawai
    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // Relasi ke Tahun Ajaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // Relasi ke Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

     // 🔑 Relasi ke detail absensi
     public function absensiDetail()
     {
         return $this->hasMany(AbsensiDetail::class, 'absensi_header_id');
     }
}
