<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;

use App\Filament\Admin\Resources\AtkKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtkKeluars extends ListRecords
{
    protected static string $resource = AtkKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
