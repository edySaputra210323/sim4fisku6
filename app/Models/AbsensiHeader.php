<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbsensiHeader extends Model
{
    use HasFactory;

    protected $table = 'absensi_header';

    protected $fillable = [
        'kelas_id',
        'pegawai_id',
        'tahun_ajaran_id',
        'semester_id',
        'tanggal',
        'status_input',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🔗 Relasi
    |--------------------------------------------------------------------------
    */

    /** Kelas terkait */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /** Guru penginput / wali kelas */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /** Tahun ajaran aktif */
    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    /** Semester aktif */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /** Detail absensi siswa */
    public function absensiDetails(): HasMany
    {
        return $this->hasMany(AbsensiDetail::class, 'absensi_header_id');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔧 Boot method
    |--------------------------------------------------------------------------
    | Supaya saat AbsensiHeader dihapus, semua detail ikut terhapus otomatis.
    */
    protected static function booted()
    {
        static::deleting(function ($absensi) {
            $absensi->absensiDetails()->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 🧮 Helper / Accessor (optional, berguna di rekap nanti)
    |--------------------------------------------------------------------------
    */

    // Jumlah siswa hadir
    public function getJumlahHadirAttribute(): int
    {
        return $this->absensiDetails()->where('status', 'hadir')->count();
    }

    // Jumlah siswa tidak hadir (izin/sakit/alpa)
    public function getJumlahTidakHadirAttribute(): int
    {
        return $this->absensiDetails()->whereIn('status', ['sakit', 'izin', 'alpa'])->count();
    }

    // Format tanggal lokal (untuk tampilan di Filament)
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal->translatedFormat('l, d F Y'); // Kamis, 16 Oktober 2025
    }
}
