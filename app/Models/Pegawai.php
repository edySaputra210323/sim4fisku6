<?php

namespace App\Models;

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
        'email',
        'nuptk',
        'npy',
        'status',
        'foto_pegawai',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'jenis_kelamin' => 'string',
    ];

    public function jabatanPegawai()
    {
        return $this->belongsTo(PosisiKepegawaian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
