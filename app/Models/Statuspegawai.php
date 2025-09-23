<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pegawai;

class Statuspegawai extends Model
{
    protected $table = 'status_pegawai';

    protected $fillable = [
        'nama_status',
        'kode',
        'warna',
        'is_active',
        'keterangan',
    ];

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'status_pegawai_id');
    }
}
