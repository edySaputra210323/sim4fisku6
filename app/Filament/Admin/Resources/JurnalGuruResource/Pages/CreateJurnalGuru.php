<?php

namespace App\Filament\Admin\Resources\JurnalGuruResource\Pages;

use Filament\Actions;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\JurnalGuruResource;

class CreateJurnalGuru extends CreateRecord
{
    protected static string $resource = JurnalGuruResource::class;

     protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

    $tahunAktif = \App\Models\TahunAjaran::where('status', 1)->first();
    $semesterAktif = \App\Models\Semester::where('status', 1)->first();

    $data['tahun_ajaran_id'] = $tahunAktif?->id;
    $data['semester_id'] = $semesterAktif?->id;

    // jika superadmin → ambil dari form, jika guru → ambil dari user
    if ($user->hasRole('superadmin')) {
        $data['pegawai_id'] = $data['pegawai_id'] ?? null;
    } else {
        $data['pegawai_id'] = $user->pegawai?->id ?? null;
    }

    if (empty($data['pegawai_id'])) {
        throw new \Exception('Pegawai ID tidak ditemukan untuk user ini.');
    }

    return $data;
    }
}
