<?php

namespace App\Models;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;
    protected $table = 'kelas';
    protected $fillable = [
        'nama_kelas',
        'wali_kelas_id'
    ];

    // Relasi ke RiwayatKelas
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'kelas_id');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Pegawai::class, 'wali_kelas_id');
    }
}
