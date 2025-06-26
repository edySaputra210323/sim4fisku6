<?php

namespace App\Filament\Admin\Resources\DataSiswaResource\Pages;

use App\Filament\Admin\Resources\DataSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDataSiswa extends ViewRecord
{
    protected static string $resource = DataSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
