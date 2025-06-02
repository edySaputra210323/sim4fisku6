<?php

namespace App\Filament\Admin\Clusters\Master\Resources\UnitResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUnit extends ViewRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
