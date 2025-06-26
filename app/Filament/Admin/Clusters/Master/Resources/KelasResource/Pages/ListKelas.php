<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KelasResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KelasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKelas extends ListRecords
{
    protected static string $resource = KelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
