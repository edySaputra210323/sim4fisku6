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
}
