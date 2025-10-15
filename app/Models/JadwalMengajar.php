<?php

namespace App\Models;

use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class JadwalMengajar extends Model
{
    protected $table = 'jadwal_mengajar';

    protected $fillable = [
        'hari',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'mapel_id',
        'kelas_id',
        'pegawai_id',
        'tahun_ajaran_id',
        'semester_id',
    ];

     protected $casts = [
        'jam_ke' => 'array',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    protected function waktuMengajar(): Attribute
    {
        return Attribute::get(function () {
            $mulai = $this->jam_mulai ? date('H:i', strtotime($this->jam_mulai)) : '-';
            $selesai = $this->jam_selesai ? date('H:i', strtotime($this->jam_selesai)) : '-';
            return "{$mulai} - {$selesai}";
        });
    }
    

    public function guru()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
