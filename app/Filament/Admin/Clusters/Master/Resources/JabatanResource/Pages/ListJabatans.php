<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JabatanResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJabatans extends ListRecords
{
    protected static string $resource = JabatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
