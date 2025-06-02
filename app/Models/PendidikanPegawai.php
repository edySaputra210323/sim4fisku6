<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendidikanPegawai extends Model
{
    protected $table = 'pendidikan_pegawai';
    protected $fillable = [
        'pegawai_id',
        'level',
        'jurusan',
        'universitas',
        'tahun_lulus',
        'no_ijazah',
        'file_ijazah',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
