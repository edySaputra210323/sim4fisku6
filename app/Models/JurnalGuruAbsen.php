<?php

namespace App\Models;

use App\Models\JurnalGuru;
use App\Models\RiwayatKelas;
use Illuminate\Database\Eloquent\Model;

class JurnalGuruAbsen extends Model
{
    protected $table = 'jurnal_guru_absen';

    protected $fillable = [
        'jurnal_guru_id',
        'riwayat_kelas_id',
        'status',
    ];

    public function jurnalGuru()
    {
        return $this->belongsTo(JurnalGuru::class);
    }

    public function riwayatKelas()
    {
        return $this->belongsTo(RiwayatKelas::class);
    }
}
