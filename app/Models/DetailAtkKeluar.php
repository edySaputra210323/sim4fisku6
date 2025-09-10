<?php

namespace App\Models;

use App\Models\Atk;
use App\Models\AtkKeluar;
use Illuminate\Database\Eloquent\Model;

class DetailAtkKeluar extends Model
{
    protected $table = 'detail_atk_keluar';

    protected $fillable = [
        'atk_keluar_id',
        'atk_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    // 🔹 Relasi ke transaksi
    public function atkKeluar()
    {
        return $this->belongsTo(AtkKeluar::class, 'atk_keluar_id');
    }

    // 🔹 Relasi ke barang ATK
    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }

    // 🚀 Boot method untuk update stok otomatis
    protected static function booted()
    {
        // Saat menambah detail → kurangi stok
        static::created(function ($detail) {
            if ($detail->atk && $detail->atkKeluar->status !== 'canceled') {
                $detail->atk->decrement('stock', $detail->qty);
            }
        });

        // Saat update detail
        static::updated(function ($detail) {
            // Kalau atk_id berubah → rollback stok lama, kurangi stok baru
            if ($detail->isDirty('atk_id')) {
                $oldAtkId = $detail->getOriginal('atk_id');
                $oldQty = $detail->getOriginal('qty');
                $newQty = $detail->qty;

                $oldAtk = Atk::find($oldAtkId);
                if ($oldAtk && $detail->atkKeluar->status !== 'canceled') {
                    $oldAtk->increment('stock', $oldQty);
                }

                if ($detail->atk && $detail->atkKeluar->status !== 'canceled') {
                    $detail->atk->decrement('stock', $newQty);
                }

                return;
            }

            // Kalau hanya qty yang berubah
            if ($detail->isDirty('qty') && $detail->atkKeluar->status !== 'canceled') {
                $oldQty = $detail->getOriginal('qty');
                $newQty = $detail->qty;
                $selisih = $newQty - $oldQty;

                if ($detail->atk) {
                    if ($selisih > 0) {
                        $detail->atk->decrement('stock', $selisih);
                    } elseif ($selisih < 0) {
                        $detail->atk->increment('stock', abs($selisih));
                    }
                }
            }
        });

        // Saat menghapus detail → kembalikan stok (kecuali transaksi sudah canceled)
        static::deleted(function ($detail) {
            if ($detail->atk && $detail->atkKeluar->status !== 'canceled') {
                $detail->atk->increment('stock', $detail->qty);
            }
        });
    }
}
