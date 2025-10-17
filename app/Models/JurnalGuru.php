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
    ];


    protected $casts = [
        'jam_ke' => 'array',
        'tanggal' => 'date',
    ];
    
    // protected function setSiswaTidakHadirAttribute($value)
    // {
    //     // Jika bentuknya string (misal: "{...}, {...}")
    //     if (is_string($value) && !str_starts_with(trim($value), '[')) {
    //         $value = "[$value]";
    //     }
    
    //     // Decode agar jadi array valid, lalu encode ulang
    //     $array = is_array($value) ? $value : json_decode($value, true);
    
    //     $this->attributes['siswa_tidak_hadir'] = json_encode($array ?? []);
    // }

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

    public function absensi()
    {
    return $this->hasMany(JurnalGuruAbsen::class, 'jurnal_guru_id');
    }
}
