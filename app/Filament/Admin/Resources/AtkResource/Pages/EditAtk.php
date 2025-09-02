<?php

namespace App\Filament\Admin\Resources\AtkResource\Pages;

use App\Filament\Admin\Resources\AtkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtk extends EditRecord
{
    protected static string $resource = AtkResource::class;

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
