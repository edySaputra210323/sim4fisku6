<?php

namespace App\Filament\Admin\Clusters\Master\Resources\StatusSiswaResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\StatusSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStatusSiswa extends CreateRecord
{
    protected static string $resource = StatusSiswaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
