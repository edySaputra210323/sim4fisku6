<?php

namespace App\Filament\Admin\Resources\IzinPegawaiResource\Pages;

use App\Filament\Admin\Resources\IzinPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIzinPegawai extends EditRecord
{
    protected static string $resource = IzinPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
