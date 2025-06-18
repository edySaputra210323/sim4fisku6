<?php

namespace App\Models;

use App\Models\User;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';
    protected $fillable = [
        'dibuat_oleh_id',
        'nm_pengirim',
        'tgl_terima',
        'no_surat',
        'tgl_surat',
        'perihal',
        'tujuan_surat',
        'file_surat',
        'status',
        'semester_id',
        'th_ajaran_id',
    ];

    protected $casts = [
        'tgl_terima' => 'date',
        'tgl_surat' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'th_ajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
