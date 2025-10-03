<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;

use App\Filament\Admin\Resources\AbsensiHeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsensiHeaders extends ListRecords
{
    protected static string $resource = AbsensiHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
