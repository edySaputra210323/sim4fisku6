<?php

namespace App\Filament\Admin\Resources\DataSiswaResource\Pages;

use App\Filament\Admin\Resources\DataSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataSiswa extends EditRecord
{
    protected static string $resource = DataSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
