<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendidikanOrtu extends Model
{
    use SoftDeletes;
    protected $table = 'pendidikan_ortu';
    protected $fillable = [
        'jenjang_pendidikan',
        'kode_jenjang_pendidikan',
    ];

    public function dataSiswaAyah()
    {
        return $this->hasMany(DataSiswa::class, 'pendidikan_ayah_id');
    }

    public function dataSiswaIbu()
    {
        return $this->hasMany(DataSiswa::class, 'pendidikan_ibu_id');
    }

    public function dataSiswaWali()
    {
        return $this->hasMany(DataSiswa::class, 'pendidikan_wali_id');
    }
}
