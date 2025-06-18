<?php

namespace App\Models;

use App\Models\User;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    // Boot method untuk handle event
    protected static function boot()
    {
        parent::boot();

        // Event deleting
        static::deleting(function ($suratMasuk) {
            if ($suratMasuk->file_surat) {
                try {
                    Storage::disk('public')->delete($suratMasuk->file_surat);
                    \Log::info("File surat masuk dihapus: {$suratMasuk->file_surat}");
                } catch (\Exception $e) {
                    \Log::error("Gagal menghapus file surat masuk: {$suratMasuk->file_surat}, Error: {$e->getMessage()}");
                }
            }
        });
    }

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
