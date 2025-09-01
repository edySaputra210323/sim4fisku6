<?php

namespace App\Models;

use App\Models\KategoriAtk;
use Illuminate\Database\Eloquent\Model;

class Atk extends Model
{
    protected $table = 'atk';

    protected $fillable = [
        'code',
        'nama_atk',
        'kategori_atk_id',
        'satuan',
        'keterangan',
        'stock',
        'foto_atk',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function kategori_atk()
    {
        return $this->belongsTo(KategoriAtk::class);
    }
}
