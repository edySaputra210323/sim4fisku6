<?php

namespace App\Filament\Admin\Resources\PendidikanPegawaiResource\Pages;

use App\Filament\Admin\Resources\PendidikanPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendidikanPegawai extends EditRecord
{
    protected static string $resource = PendidikanPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
