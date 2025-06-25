<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JarakTempuh extends Model
{
    use SoftDeletes;
    protected $table = 'jarak_tempuh';
    protected $fillable = [
        'nama_jarak_tempuh',
        'kode_jarak_tempuh',
    ];

        // Relasi ke DataSiswa
        public function dataSiswa()
        {
            return $this->hasMany(DataSiswa::class, 'jarak_tempuh_id');
        }
    
}
