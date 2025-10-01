<?php

namespace App\Models;

use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\RiwayatKelas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'riwayat_kelas_id',
        'mapel_id',
        'pegawai_id',
        'status',
        'keterangan',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
    ];

    public function riwayatKelas()
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

}
