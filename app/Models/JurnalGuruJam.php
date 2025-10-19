<?php

namespace App\Models;

use App\Models\JurnalGuru;
use Illuminate\Database\Eloquent\Model;

class JurnalGuruJam extends Model
{
    protected $table = 'jurnal_guru_jam';

    protected $fillable = [
        'jurnal_guru_id',
        'jam_ke',
    ];

    public function jurnalGuru()
    {
        return $this->belongsTo(JurnalGuru::class, 'jurnal_guru_id');
    }
}
