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

    public function getAbsensiHtmlAttribute(): string
{
    if ($this->absensi->isEmpty()) {
        return '<span style="color: #16a34a; font-weight: 500;">Semua hadir</span>';
    }

    $result = '<ul style="list-style-type: disc; margin-left: 1rem;">';
    foreach ($this->absensi as $absen) {
        $nama = e($absen->riwayatKelas?->dataSiswa?->nama_siswa ?? 'Tidak diketahui');
        $status = ucfirst($absen->status);

        // Warna hanya untuk teks status
        $color = match ($absen->status) {
            'sakit' => 'color: #f3c258;', // kuning gelap
            'izin'  => 'color: #3b88e0;', // biru
            'alpa'  => 'color: #f30808;', // merah
            default => 'color: #374151;', // abu
        };

        $result .= "
            <li style='margin-bottom: 4px;'>
                <span style='font-weight: 600;'>{$nama}</span>
                <span style='{$color} font-size: 0.875rem; margin-left: 6px;'>
                    {$status}
                </span>
            </li>";
    }

    $result .= '</ul>';
    return $result;
}

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

    public function jam()
    {
        return $this->hasMany(JurnalGuruJam::class, 'jurnal_guru_id');
    }

}
