<?php

namespace App\Filament\Admin\Resources\DataSiswaResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Admin\Resources\DataSiswaResource;

class EditDataSiswa extends EditRecord
{
    protected static string $resource = DataSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        Notification::make()
            ->title('Sukses')
            ->body('Data siswa berhasil di update')
            ->success()
            ->send();

        return $this->getResource()::getUrl('index');
    }
}
