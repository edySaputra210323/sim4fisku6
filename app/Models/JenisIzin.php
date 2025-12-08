<?php

namespace App\Models;

use App\Models\IzinPegawai;
use Illuminate\Database\Eloquent\Model;

class JenisIzin extends Model
{
    protected $table = 'jenis_izin';

    protected $fillable = [
        'nama',
        'deskripsi',
        'aktif',
    ];

    public function izinPegawai()
    {
        return $this->hasMany(IzinPegawai::class, 'jenis_izin_id');
    }
}
