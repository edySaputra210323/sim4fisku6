<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusSiswa extends Model
{
    use SoftDeletes;
    protected $table = 'status_siswa';
    protected $fillable = ['status', 'deskripsi'];

    // Relasi ke DataSiswa
    public function dataSiswa()
    {
        return $this->hasMany(DataSiswa::class, 'status_id');
    }

    // Relasi ke RiwayatKelas
    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'status_id');
    }
}
