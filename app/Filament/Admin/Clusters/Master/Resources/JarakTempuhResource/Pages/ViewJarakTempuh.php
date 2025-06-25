<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJarakTempuh extends ViewRecord
{
    protected static string $resource = JarakTempuhResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
