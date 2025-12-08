<?php

namespace App\Filament\Admin\Resources\IzinPegawaiResource\Pages;

use App\Filament\Admin\Resources\IzinPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIzinPegawais extends ListRecords
{
    protected static string $resource = IzinPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
