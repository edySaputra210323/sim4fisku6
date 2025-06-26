<?php

namespace App\Filament\Admin\Clusters\Master\Resources\StatusSiswaResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\StatusSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStatusSiswa extends ViewRecord
{
    protected static string $resource = StatusSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
