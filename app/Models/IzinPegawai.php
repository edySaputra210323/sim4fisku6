<?php

namespace App\Models;

use App\Models\JenisIzin;
use App\Enums\StatusPengajuanEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IzinPegawai extends Model
{
    use HasFactory;

    protected $table = 'izin_pegawai';

    protected $fillable = [
        'pegawai_id',
        'jenis_izin_id',
        'alasan',
        'jam',
        'status',
        'status_kepala_sekolah',
        'status_sdm',
        'catatan_kepala_sekolah',
        'catatan_sdm',
    ];

    protected $casts = [
        'status' => StatusPengajuanEnum::class,
    ];

    // Relasi ke Pegawai (user)
    public function pegawai()
    {
        return $this->belongsTo(User::class, 'pegawai_id');
    }

    public function jenisIzin()
    {
        return $this->belongsTo(JenisIzin::class, 'jenis_izin_id');
    }

    // Relasi log status
    public function statusLogs()
    {
        return $this->hasMany(IzinStatusLog::class, 'izin_id')->latest();
    }
}
