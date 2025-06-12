<?php

namespace App\Filament\Admin\Resources\PendidikanPegawaiResource\Pages;

use App\Filament\Admin\Resources\PendidikanPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPendidikanPegawai extends ViewRecord
{
    protected static string $resource = PendidikanPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
