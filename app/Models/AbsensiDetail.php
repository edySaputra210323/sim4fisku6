<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiDetail extends Model
{
    use HasFactory;

    protected $table = 'absensi_detail';

    protected $fillable = [
        'absensi_header_id',
        'riwayat_kelas_id',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'keterangan' => 'string',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 Relasi
    |--------------------------------------------------------------------------
    */

    /** Header absensi (wali kelas atau guru) */
    public function header(): BelongsTo
    {
        return $this->belongsTo(AbsensiHeader::class, 'absensi_header_id');
    }

    /** Relasi ke riwayat kelas */
    public function riwayatKelas(): BelongsTo
    {
        return $this->belongsTo(RiwayatKelas::class, 'riwayat_kelas_id');
    }

    /** Shortcut langsung ke siswa dari riwayat kelas */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(DataSiswa::class, 'data_siswa_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 🧮 Accessor & Helper
    |--------------------------------------------------------------------------
    */

    /** Warna status (misalnya untuk badge di Filament Table) */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'hadir' => 'success',
            'sakit' => 'warning',
            'izin'  => 'info',
            'alpa'  => 'danger',
            default => 'secondary',
        };
    }

    /** Label status (untuk tampilan yang rapi) */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    /** Nama siswa (langsung akses tanpa harus eager load riwayat) */
    public function getNamaSiswaAttribute(): ?string
    {
        return $this->riwayatKelas?->siswa?->nama_lengkap;
    }
}
