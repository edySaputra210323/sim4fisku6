<?php

namespace App\Filament\Admin\Resources\SertifikasiPegawaiResource\Pages;

use App\Filament\Admin\Resources\SertifikasiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSertifikasiPegawai extends EditRecord
{
    protected static string $resource = SertifikasiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
