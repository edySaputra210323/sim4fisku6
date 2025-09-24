<?php

namespace App\Models;

use App\Models\SuratMasuk;
use App\Enums\SemesterEnum;
use App\Models\SuratKeluar;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    protected $table = 'semester';

    protected $fillable = [
        'th_ajaran_id',
        'nm_semester',
        'periode_mulai',
        'periode_akhir',
        'status',
    ];

    protected $casts = [
        'status' => SemesterEnum::class,
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
    ];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'th_ajaran_id');
    }

    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'semester_id');
    }

    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'semester_id');
    }

      // Relasi ke MutasiSiswa
      public function mutasiSiswa()
      {
          return $this->hasMany(MutasiSiswa::class, 'semester_id');
      }
  
      // Relasi ke RiwayatKelas
      public function riwayatKelas()
      {
          return $this->hasMany(RiwayatKelas::class, 'semester_id');
      }
  
      // Relasi ke PrestasiPelanggaran
      public function prestasiPelanggaran()
      {
          return $this->hasMany(PrestasiPelanggaran::class, 'semester_id');
      }
  
      // Relasi ke NilaiSiswa
      public function nilaiSiswa()
      {
          return $this->hasMany(NilaiSiswa::class, 'semester_id');
      }
}
