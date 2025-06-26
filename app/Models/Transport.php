<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transport extends Model
{
    use SoftDeletes;
    protected $table = 'transport';
    protected $fillable = [
        'nama_transport',
        'kode_transport',
    ];

    public function dataSiswa()
    {
        return $this->hasMany(DataSiswa::class, 'transport_id');
    }
}
