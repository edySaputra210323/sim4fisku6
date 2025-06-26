<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PenghasilanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PenghasilanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenghasilanOrtu extends CreateRecord
{
    protected static string $resource = PenghasilanOrtuResource::class;

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
