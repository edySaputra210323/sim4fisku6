<?php

namespace App\Filament\Admin\Resources\JadwalMengajarResource\Pages;

use App\Filament\Admin\Resources\JadwalMengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalMengajar extends EditRecord
{
    protected static string $resource = JadwalMengajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getUpdatedNotificationTitle(): ?string
    {
        return 'Jadwal berhasil di update';
    }
}
