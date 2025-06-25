<?php

namespace App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJarakTempuh extends EditRecord
{
    protected static string $resource = JarakTempuhResource::class;

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
