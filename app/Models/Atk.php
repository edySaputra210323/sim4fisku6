<?php

namespace App\Models;

use App\Models\KategoriAtk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atk extends Model
{
    use SoftDeletes;
    protected $table = 'atk';

    protected $fillable = [
        'code',
        'nama_atk',
        'categori_atk_id',
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
        return $this->belongsTo(KategoriAtk::class, 'categori_atk_id', 'id');
    }
}
