<?php

namespace App\Filament\Admin\Clusters\Master\Resources\StatusPegawaiResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\StatusPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStatusPegawais extends ManageRecords
{
    protected static string $resource = StatusPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
