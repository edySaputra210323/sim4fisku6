<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJarakTempuhs extends ListRecords
{
    protected static string $resource = JarakTempuhResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
