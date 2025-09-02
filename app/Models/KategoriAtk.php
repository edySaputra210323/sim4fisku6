<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriAtk extends Model
{
    protected $table = 'kategori_atk';

    protected $fillable = [
        'nama_kategori',
    ];

    public function atk() : HasMany
    {
        return $this->hasMany(Atk::class, 'kategori_atk_id');
    }
}
