<?php

namespace App\Filament\Admin\Clusters\Master\Resources\TransportResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\TransportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTransport extends ViewRecord
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
