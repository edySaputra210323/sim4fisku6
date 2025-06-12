<?php

namespace App\Filament\Admin\Resources\SertifikasiPegawaiResource\Pages;

use App\Filament\Admin\Resources\SertifikasiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSertifikasiPegawai extends ViewRecord
{
    protected static string $resource = SertifikasiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
