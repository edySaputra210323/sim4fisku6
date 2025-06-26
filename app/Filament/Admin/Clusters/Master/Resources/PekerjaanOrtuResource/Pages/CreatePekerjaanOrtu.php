<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PekerjaanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PekerjaanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePekerjaanOrtu extends CreateRecord
{
    protected static string $resource = PekerjaanOrtuResource::class;

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
