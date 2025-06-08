<?php

namespace App\Filament\Admin\Clusters\Master\Resources\TahunAjaranResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\TahunAjaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTahunAjaran extends EditRecord
{
    protected static string $resource = TahunAjaranResource::class;

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
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['status'] ?? false) {
            $this->getModel()::where('id', '!=', $this->record->id)
                ->update(['status' => false]);
        }
        
        return $data;
    }
}
