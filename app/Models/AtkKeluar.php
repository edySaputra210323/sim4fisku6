<?php

namespace App\Models;

use App\Models\DetailAtkKeluar;
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
    ];

    protected $casts = [
        'tanggal' => 'datetime',
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
        if ($this->status !== 'draft') return;

        foreach ($this->details as $detail) {
            if ($detail->atk) {
                $detail->atk->increment('stock', $detail->qty);
            }
        }

        $this->update([
            'status' => 'canceled',
            'canceled_by_id' => auth()->id(),
            'canceled_at' => now(),
            'alasan_batal' => $alasan,
        ]);
    }
}
