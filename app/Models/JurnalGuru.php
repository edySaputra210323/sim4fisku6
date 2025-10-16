<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalGuru extends Model
{
    use HasFactory;

    protected $table = 'jurnal_guru';

    protected $fillable = [
        'pegawai_id',
        'kelas_id',
        'mapel_id',
        'tahun_ajaran_id',
        'semester_id',
        'tanggal',
        'jam_ke',
        'materi',
        'kegiatan',
        'siswa_tidak_hadir',
    ];

    protected $casts = [
        'jam_ke' => 'array',
        'siswa_tidak_hadir' => 'array',
        'tanggal' => 'date',
    ];

    // 🔹 Relasi
    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
