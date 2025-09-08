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
            if ($detail->atk) {
                $detail->atk->decrement('stock', $detail->qty);
            }
        });

        // Saat update detail (misal qty berubah)
        static::updated(function ($detail) {
            if ($detail->isDirty('qty')) {
                $oldQty = $detail->getOriginal('qty');
                $newQty = $detail->qty;
                $selisih = $newQty - $oldQty;

                if ($detail->atk) {
                    // kalau selisih positif berarti stok keluar tambahan
                    $detail->atk->decrement('stock', $selisih);
                }
            }
        });

        // Saat menghapus detail → kembalikan stok
        static::deleted(function ($detail) {
            if ($detail->atk) {
                $detail->atk->increment('stock', $detail->qty);
            }
        });
    }
}
