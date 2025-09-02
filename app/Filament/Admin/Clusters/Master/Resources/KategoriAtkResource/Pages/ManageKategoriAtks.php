<?php

namespace App\Filament\Admin\Clusters\Master\Resources\KategoriAtkResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\KategoriAtkResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageKategoriAtks extends ManageRecords
{
    protected static string $resource = KategoriAtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
