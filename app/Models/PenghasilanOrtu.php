<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenghasilanOrtu extends Model
{
    use SoftDeletes;
    protected $table = 'penghasilan_ortu';
    protected $fillable = [
        'penghasilan',
        'kode_penghasilan',
    ];

    public function dataSiswaAyah()
    {
        return $this->hasMany(DataSiswa::class, 'penghasilan_ayah_id');
    }

    // Relasi ke DataSiswa (ibu)
    public function dataSiswaIbu()
    {
        return $this->hasMany(DataSiswa::class, 'penghasilan_ibu_id');
    }

    // Relasi ke DataSiswa (wali)
    public function dataSiswaWali()
    {
        return $this->hasMany(DataSiswa::class, 'penghasilan_wali_id');
    }
}
