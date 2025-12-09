<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JenisIzinResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JenisIzinResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageJenisIzins extends ManageRecords
{
    protected static string $resource = JenisIzinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
