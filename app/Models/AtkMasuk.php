<?php

namespace App\Models;

use App\Models\Atk;
use App\Models\User;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AtkMasuk extends Model
{
    use SoftDeletes;
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
        'tanggal' => 'date',
    ];

    public function atk()
    {
        return $this->belongsTo(Atk::class,'atk_id','id');
    }

    public function tahun_ajaran()
    {
        return $this->belongsTo(TahunAjaran::class,'tahun_ajaran_id','id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class,'semester_id','id');
    }

    public function ditambahOleh()
    {
        return $this->belongsTo(User::class, 'ditambah_oleh_id','id');
    }

    public static function boot()
    {
        parent::boot();

        // Hitung total_harga sebelum menyimpan
        static::saving(function ($atkMasuk) {
            $atkMasuk->total_harga = $atkMasuk->qty * $atkMasuk->harga_satuan;
            \Log::info('Saving AtkMasuk: ' . json_encode($atkMasuk->toArray()));
        });

        // Tambah stok saat transaksi dibuat
        static::created(function ($atkMasuk) {
            $atk = $atkMasuk->atk;
            $atk->stock += $atkMasuk->qty;
            $atk->save();
            \Log::info("Added {$atkMasuk->qty} to stock of ATK ID {$atk->id}. New stock: {$atk->stock}");
        });

        // Perbarui stok saat transaksi diedit
        static::updating(function ($atkMasuk) {
            if ($atkMasuk->isDirty('qty')) {
                $oldQty = $atkMasuk->getOriginal('qty');
                $newQty = $atkMasuk->qty;
                $atk = $atkMasuk->atk;
                $atk->stock = $atk->stock - $oldQty + $newQty;
                $atk->save();
                \Log::info("Updated stock of ATK ID {$atk->id}. Old qty: {$oldQty}, New qty: {$newQty}, New stock: {$atk->stock}");
            }
            if ($atkMasuk->isDirty('foto_nota') && $atkMasuk->getOriginal('foto_nota')) {
                $oldFile = $atkMasuk->getOriginal('foto_nota');
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                    \Log::info('Deleted old foto_nota on update: ' . $oldFile);
                }
            }
        });

        // Kurangi stok dan hapus foto_nota saat transaksi dihapus (soft delete)
        static::deleted(function ($atkMasuk) {
            $atk = $atkMasuk->atk;
            $atk->stock -= $atkMasuk->qty;
            $atk->save();
            \Log::info("Subtracted {$atkMasuk->qty} from stock of ATK ID {$atk->id}. New stock: {$atk->stock}");

            // Hapus foto_nota hanya jika tidak ada record lain dengan nota yang sama
            if ($atkMasuk->nota && $atkMasuk->foto_nota) {
                $sameNotaCount = AtkMasuk::where('nota', $atkMasuk->nota)
                    ->where('id', '!=', $atkMasuk->id)
                    ->whereNull('deleted_at')
                    ->count();
                if ($sameNotaCount === 0 && Storage::disk('public')->exists($atkMasuk->foto_nota)) {
                    Storage::disk('public')->delete($atkMasuk->foto_nota);
                    \Log::info('Deleted foto_nota on soft delete: ' . $atkMasuk->foto_nota);
                }
            }
        });

        // Tambah kembali stok saat transaksi dipulihkan (restore)
        static::restored(function ($atkMasuk) {
            $atk = $atkMasuk->atk;
            $atk->stock += $atkMasuk->qty;
            $atk->save();
            \Log::info("Restored {$atkMasuk->qty} to stock of ATK ID {$atk->id}. New stock: {$atk->stock}");
        });
    }
}
