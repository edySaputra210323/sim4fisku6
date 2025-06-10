<?php

namespace App\Filament\Admin\Resources\PosisiKepegawaianResource\Pages;

use App\Filament\Admin\Resources\PosisiKepegawaianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosisiKepegawaians extends ListRecords
{
    protected static string $resource = PosisiKepegawaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
