<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJarakTempuh extends CreateRecord
{
    protected static string $resource = JarakTempuhResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }
}
