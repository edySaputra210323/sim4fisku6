<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPendidikanOrtu extends EditRecord
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
