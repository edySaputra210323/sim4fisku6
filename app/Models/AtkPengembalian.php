<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtkPengembalian extends Model
{
    protected $table = 'atk_pengembalian';

    protected $fillable = [
        'atk_keluar_id',
        'atk_id',
        'qty',
        'alasan',
        'diterima_oleh_id',
    ];

    protected static function booted()
    {
        // Ketika ada pengembalian, stok bertambah lagi
        static::created(function ($pengembalian) {
            if ($pengembalian->atk) {
                $pengembalian->atk->increment('stock', $pengembalian->qty);
            }
        });
    }

    // Relasi ke transaksi keluar
    public function atkKeluar()
    {
        return $this->belongsTo(AtkKeluar::class, 'atk_keluar_id');
    }

    // Relasi ke barang ATK
    public function atk()
    {
        return $this->belongsTo(Atk::class, 'atk_id');
    }

    // Relasi ke user penerima (admin ATK)
    public function diterimaOleh()
    {
        return $this->belongsTo(User::class, 'diterima_oleh_id');
    }
}
