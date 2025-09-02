<?php

namespace App\Filament\Admin\Resources\AtkResource\Pages;

use App\Filament\Admin\Resources\AtkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAtks extends ListRecords
{
    protected static string $resource = AtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
