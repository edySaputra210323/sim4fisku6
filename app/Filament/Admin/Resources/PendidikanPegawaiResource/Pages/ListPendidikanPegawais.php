<?php

namespace App\Filament\Admin\Resources\PendidikanPegawaiResource\Pages;

use App\Filament\Admin\Resources\PendidikanPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendidikanPegawais extends ListRecords
{
    protected static string $resource = PendidikanPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
