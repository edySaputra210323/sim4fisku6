<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinStatusLog extends Model
{
    protected $table = 'izin_status_log';

    protected $fillable = [
        'izin_id',
        'from_status',
        'to_status',
        'changed_by',
        'reason',
        'changed_at',
    ];

    public function izin()
    {
        return $this->belongsTo(IzinPegawai::class, 'izin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}