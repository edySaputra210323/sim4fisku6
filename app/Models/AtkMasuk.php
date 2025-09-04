<?php

namespace App\Models;

use App\Models\User;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\DetailAtkMasuk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon; // <-- penting

class AtkMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'atk_masuk';

    protected $fillable = [
        'nomor_nota',
        'file_nota',
        'tanggal',
        'tahun_ajaran_id',
        'semester_id',
        'ditambah_oleh_id',
    ];

    // <-- ini bikin $this->tanggal otomatis jadi Carbon saat diakses
    protected $casts = [
        'tanggal' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Pastikan $tanggal adalah Carbon, bukan string
            $tanggal = $model->tanggal
                ? Carbon::parse($model->tanggal)
                : now();

            // Auto-generate jika user tidak mengisi nomor nota supplier
            if (blank($model->nomor_nota)) {
                $prefix = 'ATK-' . $tanggal->format('Ymd') . '-';

                // Ambil nomor_nota terakhir di tanggal yang sama, lalu increment
                $last = self::withTrashed()
                    ->whereDate('tanggal', $tanggal->toDateString())
                    ->where('nomor_nota', 'like', $prefix . '%')
                    ->orderBy('nomor_nota', 'desc')
                    ->value('nomor_nota');

                $next = 1;
                if ($last) {
                    $lastSeq = (int) substr($last, strlen($prefix));
                    $next = $lastSeq + 1;
                }

                $candidate = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);

                // Jaga-jaga kalau ada race condition
                while (self::withTrashed()->where('nomor_nota', $candidate)->exists()) {
                    $next++;
                    $candidate = $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
                }

                $model->nomor_nota = $candidate;
            }
        });

        // Hapus file nota jika record dihapus permanen
        static::forceDeleted(function ($model) {
            if ($model->file_nota && Storage::disk('public')->exists($model->file_nota)) {
                Storage::disk('public')->delete($model->file_nota);
            }
        });

        // Update: jika file_nota diganti
        static::updating(function ($model) {
            if ($model->isDirty('file_nota') && $model->getOriginal('file_nota')) {
                $oldFile = $model->getOriginal('file_nota');
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }
        });
    }

    // 🔹 Relasi ke detail barang
    public function details()
    {
        return $this->hasMany(DetailAtkMasuk::class, 'atk_masuk_id');
    }

    // 🔹 Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class, 'ditambah_oleh_id');
    }

    // 🔹 Relasi ke tahun ajaran & semester
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }
}
