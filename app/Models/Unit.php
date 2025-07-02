<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'unit';

    protected $fillable = [
        'nm_unit',
        'kode_unit',
        'deskripsi',
    ];

    public function posisiKepegawaian()
    {
        return $this->hasMany(PosisiKepegawaian::class);
    }
}
