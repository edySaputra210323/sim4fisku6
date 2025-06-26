<?php

namespace App\Filament\Admin\Clusters\Master\Resources\MapelResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\MapelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMapel extends CreateRecord
{
    protected static string $resource = MapelResource::class;

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
