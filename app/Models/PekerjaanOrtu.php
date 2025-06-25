<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PekerjaanOrtu extends Model
{
    use SoftDeletes;
    protected $table = 'pekerjaan_ortu';
    protected $fillable = [
        'nama_pekerjaan',
        'kode_pekerjaan',
    ];

    public function dataSiswaAyah()
    {
        return $this->hasMany(DataSiswa::class, 'pekerjaan_ayah_id');
    }

    public function dataSiswaIbu()
    {
        return $this->hasMany(DataSiswa::class, 'pekerjaan_ibu_id');
    }

    public function dataSiswaWali()
    {
        return $this->hasMany(DataSiswa::class, 'pekerjaan_wali_id');
    }
}
