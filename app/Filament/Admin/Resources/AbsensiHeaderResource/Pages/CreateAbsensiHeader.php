<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;

use Filament\Actions;
use App\Models\RiwayatKelas;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\AbsensiHeaderResource;

class CreateAbsensiHeader extends CreateRecord
{
    protected static string $resource = AbsensiHeaderResource::class;

    protected function afterCreate(): void
    {
    $riwayatKelas = RiwayatKelas::where('kelas_id', $this->record->kelas_id)
        ->where('tahun_ajaran_id', $this->record->tahun_ajaran_id)
        ->where('semester_id', $this->record->semester_id)
        ->get();


        foreach ($riwayatKelas as $riwayat) {
            $this->record->absensiDetails()->create([
                'riwayat_kelas_id' => $riwayat->id,
                'status' => 'hadir', // default
                'keterangan' => null,
            ]);
         }
    }
}
