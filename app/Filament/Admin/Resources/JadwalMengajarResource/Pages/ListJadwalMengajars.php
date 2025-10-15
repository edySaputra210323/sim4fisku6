<?php

namespace App\Filament\Admin\Resources\JadwalMengajarResource\Pages;

use App\Filament\Admin\Resources\JadwalMengajarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJadwalMengajars extends ListRecords
{
    protected static string $resource = JadwalMengajarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
