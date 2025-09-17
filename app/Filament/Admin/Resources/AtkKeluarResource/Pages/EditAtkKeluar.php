<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;

use App\Filament\Admin\Resources\AtkKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtkKeluar extends EditRecord
{
    protected static string $resource = AtkKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // delegasikan semua logic status ke model
        $this->record->applyStatus($data['status'], $data['alasan_batal'] ?? null);

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
