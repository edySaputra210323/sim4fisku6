<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriAtk extends Model
{
    protected $table = 'kategori_atk';

    protected $fillable = [
        'nama_kategori',
    ];

    public function atk()
    {
        return $this->hasMany(Atk::class);
    }
}
