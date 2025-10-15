<?php

namespace App\Filament\Admin\Resources\JadwalMengajarResource\Pages;

use Filament\Actions;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\JadwalMengajarResource;

class CreateJadwalMengajar extends CreateRecord
{
    protected static string $resource = JadwalMengajarResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Ambil ID tahun ajaran dan semester yang aktif
    //     $data['tahun_ajaran_id'] = TahunAjaran::where('status', 1)->value('id');
    //     $data['semester_id'] = Semester::where('status', 1)->value('id');

    //     return $data;
    // }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Jadwal Mengajar berhasil dibuat!';
    }
}
