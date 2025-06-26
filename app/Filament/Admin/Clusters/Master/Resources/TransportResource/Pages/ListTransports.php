<?php

namespace App\Filament\Admin\Clusters\Master\Resources\TransportResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\TransportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransports extends ListRecords
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
