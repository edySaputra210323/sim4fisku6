<?php

namespace App\Filament\Admin\Resources\JurnalGuruResource\Pages;

use App\Filament\Admin\Resources\JurnalGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJurnalGurus extends ListRecords
{
    protected static string $resource = JurnalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
