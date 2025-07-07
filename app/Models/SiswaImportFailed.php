<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaImportFailed extends Model
{
    protected $table = 'siswa_import_faileds';

    protected $fillable = [
        'nama_siswa',
        'nis',
        'nisn',
        'virtual_account',
        'no_hp',
        'email',
        'agama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'jarak_tempuh_id',
        'transport_id',
        'angkatan',
        'tanggal_masuk',
        'status_id',
        'nm_ayah',
        'nm_ibu',
        'ditambah_oleh',
        'catatan_gagal',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'jenis_kelamin' => 'string',
    ];

    public function getNmSiswaFullAttribute()
    {
        if (!empty($this->nisn)) {
            return $this->nisn . ' - ' . $this->nama_siswa;
        }
        return $this->nama_siswa;
    }

    public function jarakTempuh(): BelongsTo
    {
        return $this->belongsTo(JarakTempuh::class);
    }

    public function transport(): BelongsTo
    {
        return $this->belongsTo(Transport::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusSiswa::class);
    }

    public function ditambahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dihapusOleh(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}