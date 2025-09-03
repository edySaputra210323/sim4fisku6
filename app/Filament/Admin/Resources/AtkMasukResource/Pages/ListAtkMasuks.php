<?php

namespace App\Filament\Admin\Resources\AtkMasukResource\Pages;

use App\Filament\Admin\Resources\AtkMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtkMasuks extends ListRecords
{
    protected static string $resource = AtkMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
