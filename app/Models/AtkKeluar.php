<?php

namespace App\Models;

use App\Models\DetailAtkKeluar;
use App\Models\Pegawai;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\AtkPengembalian;
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
        'tanggal' => 'datetime',
        'verified_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    // 🔹 Relasi ke detail
    public function details()
    {
        return $this->hasMany(DetailAtkKeluar::class, 'atk_keluar_id');
    }

    // 🔹 Relasi ke pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
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


    public function verify()
    {
        if ($this->status !== 'draft') return;

        $this->update([
            'status' => 'verified',
            'verified_by_id' => auth()->id(),
            'verified_at' => now(),
        ]);
    }

    public function cancel($alasan = null)
    {
        // Kalau sudah canceled, skip
        if ($this->status === 'canceled') {
            return;
        }

        // 🔹 Batasan role
        $user = auth()->user();
        if ($this->status === 'draft') {
            // draft boleh dibatalkan oleh pemilik atau admin
            if ($user->id !== $this->ditambah_oleh_id && !$user->hasRole('superadmin')) {
                throw new \Exception("Anda tidak berhak membatalkan transaksi ini.");
            }
        }

        if ($this->status === 'verified') {
            // hanya admin/superadmin boleh cancel transaksi yang sudah diverifikasi
            if (!$user->hasRole('superadmin')) {
                throw new \Exception("Hanya superadmin yang dapat membatalkan transaksi terverifikasi.");
            }

            // rollback stok
            foreach ($this->details as $detail) {
                if ($detail->atk) {
                    $detail->atk->increment('stock', $detail->qty);
                }
            }
        }

        // update status canceled
        $this->update([
            'status' => 'canceled',
            'canceled_by_id' => $user->id,
            'canceled_at' => now(),
            'alasan_batal' => $alasan,
        ]);
    }


}
