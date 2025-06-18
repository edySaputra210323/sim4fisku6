<?php

namespace App\Filament\Admin\Resources\SuratMasukResource\Pages;

use App\Filament\Admin\Resources\SuratMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratMasuks extends ListRecords
{
    protected static string $resource = SuratMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
