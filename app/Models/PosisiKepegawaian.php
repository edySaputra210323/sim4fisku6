<?php

namespace App\Models;

use App\Models\Jabatan;
use App\Models\Unit;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Model;

class PosisiKepegawaian extends Model
{
    protected $table = 'posisi_kepegawaian';
    protected $fillable = [
        'pegawai_id',
        'jabatan_id',
        'unit_id',
        'status',
        'no_sk_pengangkatan',
        'start_date',
        'end_date',
        'akhir_tanggal',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string',
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
