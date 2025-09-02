<?php

namespace App\Models;

use App\Models\KategoriAtk;
use Illuminate\Database\Eloquent\Model;
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
        }
}
