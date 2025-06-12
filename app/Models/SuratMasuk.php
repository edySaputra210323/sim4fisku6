<?php

namespace App\Models;

use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    protected $table = 'surat_masuk';
    protected $fillable = [
        'pegawai_id',
        'nm_pengirim',
        'tgl_terima',
        'no_surat',
        'tgl_surat',
        'perihal',
        'asal_surat',
        'file_surat',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tgl_terima' => 'date',
        'tgl_surat' => 'date',
    ];

    // Relasi ke Pegawai untuk pegawai_id
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    // Relasi ke Pegawai untuk created_by
    public function createdBy()
    {
        return $this->belongsTo(Pegawai::class, 'created_by');
    }

    // Relasi ke Pegawai untuk updated_by
    public function updatedBy()
    {
        return $this->belongsTo(Pegawai::class, 'updated_by');
    }

     // Mengisi created_by dan updated_by secara otomatis
     protected static function boot()
     {
         parent::boot();
 
         static::creating(function ($model) {
             if (auth()->check() && auth()->user()->pegawai) {
                 $model->created_by = auth()->user()->pegawai->id;
             }
         });
 
         static::updating(function ($model) {
             if (auth()->check() && auth()->user()->pegawai) {
                 $model->updated_by = auth()->user()->pegawai->id;
             }
         });
     }
}
