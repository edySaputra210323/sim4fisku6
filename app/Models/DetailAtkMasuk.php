<?php

namespace App\Models;

use App\Models\Atk;
use App\Models\AtkMasuk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailAtkMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'detail_atk_masuk';

    protected $fillable = [
        'atk_masuk_id',
        'atk_id',
        'qty',
        'harga_satuan',
        'total_harga',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    // 🔗 Relasi ke transaksi
    public function atkMasuk()
    {
        return $this->belongsTo(AtkMasuk::class, 'atk_masuk_id');
    }

    // 🔗 Relasi ke barang ATK
    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }

    // 🚀 Boot method untuk update stok & hitung total otomatis
    protected static function booted()
    {
        // Hitung total_harga sebelum simpan (baik create/update)
        static::saving(function ($detail) {
            $detail->total_harga = ($detail->qty ?? 0) * ($detail->harga_satuan ?? 0);
        });

        // Saat menambah detail
        static::created(function ($detail) {
            if ($detail->atk) {
                $detail->atk->increment('stock', $detail->qty);
            }
        });

        // Saat mengupdate detail (misal qty berubah)
        static::updated(function ($detail) {
            if ($detail->isDirty('qty')) {
                $oldQty = $detail->getOriginal('qty');
                $newQty = $detail->qty;
                $selisih = $newQty - $oldQty;

                if ($detail->atk) {
                    $detail->atk->increment('stock', $selisih);
                }
            }
        });

        // Saat menghapus detail
        static::deleted(function ($detail) {
            if ($detail->atk) {
                $detail->atk->decrement('stock', $detail->qty);
            }
        });
    }
}
