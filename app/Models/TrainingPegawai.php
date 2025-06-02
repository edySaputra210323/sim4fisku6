<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingPegawai extends Model
{
    protected $table = 'training_pegawai';
    protected $fillable = [
        'pegawai_id',
        'nm_training',
        'penyelenggara',
        'start_date',
        'end_date',
        'duration_hours',
        'no_sertifikat',
        'file_sertifikat_training', 
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
