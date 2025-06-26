<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PenghasilanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PenghasilanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenghasilanOrtus extends ListRecords
{
    protected static string $resource = PenghasilanOrtuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
