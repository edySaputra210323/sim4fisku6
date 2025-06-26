<?php

namespace App\Filament\Admin\Resources\DataSiswaResource\Pages;

use App\Filament\Admin\Resources\DataSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataSiswas extends ListRecords
{
    protected static string $resource = DataSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
