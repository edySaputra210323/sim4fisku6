<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        // 'email_pegawai',
        'nuptk',
        'npy',
        'status',
        'foto_pegawai',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'jenis_kelamin' => 'string',
    ];

    // Boot method untuk handle event
    protected static function boot()
    {
        parent::boot();

        // Event deleting
        static::deleting(function ($pegawai) {
            // Hapus file foto dari storage
            if ($pegawai->foto_pegawai) {
                Storage::disk('public')->delete($pegawai->foto_pegawai);
            }

            // Hapus user terkait (jika ada)
            if ($pegawai->user) {
                $pegawai->user->delete();
            }
        });
    }

    public function getTempatTanggalLahirAttribute()
    {
    // Atur locale Carbon ke Indonesia
    Carbon::setLocale('id');

    // Format tanggal dengan bulan penuh
    $formattedDate = $this->tgl_lahir ? $this->tgl_lahir->translatedFormat('d F Y') : '';
    $tempatLahir = $this->tempat_lahir ?? '';

    return $tempatLahir && $formattedDate ? $tempatLahir . ', ' . $formattedDate : ($tempatLahir ?: $formattedDate);
    }

    // Accessor untuk foto_pegawai
    public function getFotoPegawaiAttribute($value)
    {
        return $value ? asset('storage/' . $value) : asset('images/no_pic.png');
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
