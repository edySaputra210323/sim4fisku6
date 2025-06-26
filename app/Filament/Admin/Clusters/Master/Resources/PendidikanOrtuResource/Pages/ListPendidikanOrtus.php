<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendidikanOrtus extends ListRecords
{
    protected static string $resource = PendidikanOrtuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
