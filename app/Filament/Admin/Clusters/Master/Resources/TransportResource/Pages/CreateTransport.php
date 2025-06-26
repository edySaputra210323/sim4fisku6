<?php

namespace App\Filament\Admin\Clusters\Master\Resources\TransportResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\TransportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransport extends CreateRecord
{
    protected static string $resource = TransportResource::class;

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
