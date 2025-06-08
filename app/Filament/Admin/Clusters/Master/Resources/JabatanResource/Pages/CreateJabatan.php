<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JabatanResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JabatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJabatan extends CreateRecord
{
    protected static string $resource = JabatanResource::class;

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
