<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JabatanResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJabatan extends ViewRecord
{
    protected static string $resource = JabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
