<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtkKeluar extends Model
{
    use SoftDeletes;

    protected $table = 'atk_keluar';

    protected $fillable = [
        'tanggal',
        'pegawai_id',
        'tahun_ajaran_id',
        'semester_id',
        'ditambah_oleh_id',
        'status',
        'verified_by_id',
        'verified_at',
        'canceled_by_id',
        'canceled_at',
        'alasan_batal',
    ];

    protected $casts = [
        'tanggal'     => 'datetime',
        'verified_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */
    public function details()
    {
        return $this->hasMany(DetailAtkKeluar::class, 'atk_keluar_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'ditambah_oleh_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function pengembalian()
    {
        return $this->hasMany(AtkPengembalian::class, 'atk_keluar_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by_id');
    }

    public function canceledBy()
    {
        return $this->belongsTo(User::class, 'canceled_by_id');
    }

    public function ditambahOleh()
    {
        return $this->belongsTo(User::class, 'ditambah_oleh_id');
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HANDLERS
    |--------------------------------------------------------------------------
    */
    public function verify(): void
    {
        if ($this->status === 'verified') {
            return; // sudah diverifikasi
        }

        $this->update([
            'status'         => 'verified',
            'verified_by_id' => auth()->id(),
            'verified_at'    => now(),
        ]);
    }

    public function cancel(?string $alasan = null): void
    {
        if ($this->status === 'canceled') {
            return; // sudah dibatalkan
        }

        $user = auth()->user();

        // kalau draft → hanya pemilik / superadmin boleh cancel
        if ($this->status === 'draft') {
            if ($user->id !== $this->ditambah_oleh_id && !$user->hasRole('superadmin')) {
                throw new \Exception("Anda tidak berhak membatalkan transaksi ini.");
            }

            // rollback stok juga
            $this->loadMissing('details.atk');
            foreach ($this->details as $detail) {
                if ($detail->atk) {
                    $detail->atk->increment('stock', $detail->qty);
                }
            }
        }

        // kalau verified → hanya superadmin boleh cancel & rollback stok
        if ($this->status === 'verified') {
            if (!$user->hasRole('superadmin')) {
                throw new \Exception("Hanya superadmin yang dapat membatalkan transaksi terverifikasi.");
            }

            // rollback stok
            $this->loadMissing('details.atk');
            foreach ($this->details as $detail) {
                if ($detail->atk) {
                    $detail->atk->increment('stock', $detail->qty);
                }
            }
        }

        $this->update([
            'status'         => 'canceled',
            'canceled_by_id' => $user->id,
            'canceled_at'    => now(),
            'alasan_batal'   => $alasan,
        ]);
    }

    public function applyStatus(string $status, ?string $alasan = null): void
    {
        if ($status === 'verified') {
            $this->verify();
        }

        if ($status === 'canceled') {
            $this->cancel($alasan);
        }
    }
}
