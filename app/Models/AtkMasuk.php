<?php

namespace App\Models;

use App\Models\Atk;
use App\Models\User;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;


class AtkMasuk extends Model
{
    protected $table = 'atk_masuk';

    protected $fillable = [
        'atk_id',
        'qty',
        'harga_satuan',
        'total_harga',
        'tanggal',
        'nota',
        'tahun_ajaran_id',
        'semester_id',
        'ditambah_oleh_id',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function atk()
    {
        return $this->belongsTo(Atk::class);
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function ditambahOleh()
    {
        return $this->belongsTo(User::class, 'ditambah_oleh_id');
    }

    public static function boot()
    {
        parent::boot();

        // Hitung total_harga sebelum menyimpan
        static::saving(function ($atkMasuk) {
            $atkMasuk->total_harga = $atkMasuk->qty * $atkMasuk->harga_satuan;
        });

        // Tambah stok ke tabel atk saat transaksi dibuat
        static::created(function ($atkMasuk) {
            $atk = $atkMasuk->atk;
            $atk->stock += $atkMasuk->qty;
            $atk->save();
        });

        // Kurangi stok jika transaksi dihapus
        static::deleted(function ($atkMasuk) {
            $atk = $atkMasuk->atk;
            $atk->stock -= $atkMasuk->qty;
            $atk->save();
        });
    }
}
