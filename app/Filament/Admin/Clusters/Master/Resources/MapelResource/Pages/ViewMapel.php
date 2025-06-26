<?php

namespace App\Filament\Admin\Clusters\Master\Resources\MapelResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\MapelResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMapel extends ViewRecord
{
    protected static string $resource = MapelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
