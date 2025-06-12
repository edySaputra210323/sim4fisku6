<?php

namespace App\Filament\Admin\Resources\SertifikasiPegawaiResource\Pages;

use App\Filament\Admin\Resources\SertifikasiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSertifikasiPegawais extends ListRecords
{
    protected static string $resource = SertifikasiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
