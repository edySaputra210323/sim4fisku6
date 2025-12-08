<?php

namespace App\Filament\Admin\Resources\IzinPegawaiResource\Pages;

use App\Filament\Admin\Resources\IzinPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIzinPegawai extends ViewRecord
{
    protected static string $resource = IzinPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
