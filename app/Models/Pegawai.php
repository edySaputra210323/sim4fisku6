<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = [
        'user_id',
        'nik',
        'nm_pegawai',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'phone',
        // 'email',
        'nuptk',
        'npy',
        'status',
        'foto_pegawai',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'jenis_kelamin' => 'string',
    ];

    public function getTempatTanggalLahirAttribute()
    {
    // Atur locale Carbon ke Indonesia
    Carbon::setLocale('id');

    // Format tanggal dengan bulan penuh
    $formattedDate = $this->tgl_lahir ? $this->tgl_lahir->translatedFormat('d F Y') : '';
    $tempatLahir = $this->tempat_lahir ?? '';

    return $tempatLahir && $formattedDate ? $tempatLahir . ', ' . $formattedDate : ($tempatLahir ?: $formattedDate);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posisiKepegawaian()
    {
        return $this->hasMany(PosisiKepegawaian::class);
    }

    public function pendidikan()
    {
        return $this->hasMany(PendidikanPegawai::class);
    }

    public function sertifikasi()
    {
        return $this->hasMany(SertifikasiPegawai::class);
    }

    public function training()
    {
        return $this->hasMany(TrainingPegawai::class);
    }
}
