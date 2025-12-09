<?php

namespace App\Models;

use App\Models\IzinPegawai;
use App\Enums\StatusPengajuanEnum;
use Illuminate\Database\Eloquent\Model;

class JenisIzin extends Model
{
    protected $table = 'jenis_izin';

    protected $fillable = [
        'nama_jenis_izin',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'status'
    ];

    public function izinPegawai()
    {
        return $this->hasMany(IzinPegawai::class, 'jenis_izin_id');
    }
}
