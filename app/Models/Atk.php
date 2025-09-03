<?php

namespace App\Models;

use App\Models\KategoriAtk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Atk extends Model
{
    use SoftDeletes;
    protected $table = 'atk';

    protected $fillable = [
        'code',
        'nama_atk',
        'kategori_atk_id',
        'satuan',
        'keterangan',
        'stock',
        'foto_atk',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    public function kategoriAtk()
    {
        return $this->belongsTo(KategoriAtk::class, 'kategori_atk_id');
    }

    protected static function boot()
        {
            parent::boot();
            
            static::creating(function ($model) {
                if (empty($model->code)) {
                    // Ambil kategori
                    $kategori = KategoriAtk::find($model->kategori_atk_id);
                    
                    if ($kategori) {
                        // Ambil 3 huruf pertama kategori, uppercase
                        $kodeKategori = strtoupper(substr($kategori->nama_kategori, 0, 3));
                        
                        // Hitung berapa ATK dengan kategori yang sama
                        $count = Atk::where('kategori_atk_id', $model->kategori_atk_id)->count() + 1;
                        
                        // Format: PEN-0001, KER-0001, dll
                        $model->code = $kodeKategori . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
                    } else {
                        // Fallback jika kategori tidak ditemukan
                        $model->code = 'ATK-' . str_pad(Atk::count() + 1, 4, '0', STR_PAD_LEFT);
                    }
                }
            });

            // hapus file saat record dihapus soft delete
            static::deleting(function($model){
                if($model->foto_atk && Storage::disk('public')->exists($model->foto_atk)){
                    Storage::disk('public')->delete($model->foto_atk);
                    \Log::info('deleted file on soft delete: ' . $model->foto_atk);
                }
            });
            // saat record dihapus permanen (force delete)
            static::forceDeleted(function ($model){
                if($model->foto_atk && Storage::disk('public')->exists($model->foto_atk)){
                    Storage::disk('public')->delete($model->foto_atk);
                    \Log::info('deleted file on force delete: ' . $model->foto_atk);
                }
            });
            // saat record di edit dan foto_atk di ganti
            static::updating(function ($model) {
                if ($model->isDirty('foto_atk') && $model->getOriginal('foto_atk')) {
                    $oldFile = $model->getOriginal('foto_atk');
                    if (Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                        \Log::info('Deleted old file on update: ' . $oldFile);
                    }
                }
            });
            // Logging saat menyimpan record
            static::saving(function ($model) {
                \Log::info('Saving Atk model with foto_atk: ' . ($model->foto_atk ?? 'No file'));
            });
        }
}
