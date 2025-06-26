<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePendidikanOrtu extends CreateRecord
{
    protected static string $resource = PendidikanOrtuResource::class;

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
