<?php

namespace App\Models;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Model;

class PosisiKepegawaian extends Model
{
    protected $table = 'posisi_kepegawaian';
    protected $fillable = [
        'pegawai_id',
        'posisi',
        'mulai_tanggal',
        'akhir_tanggal',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
