<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PekerjaanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PekerjaanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPekerjaanOrtus extends ListRecords
{
    protected static string $resource = PekerjaanOrtuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
