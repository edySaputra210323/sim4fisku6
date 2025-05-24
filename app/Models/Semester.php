<?php

namespace App\Models;

use App\Models\SuratKeluar;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'th_ajaran_id',
        'nm_semester',
        'periode_mulai',
        'periode_akhir',
        'status',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'th_ajaran_id');
    }

    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'semester_id');
    }
}
