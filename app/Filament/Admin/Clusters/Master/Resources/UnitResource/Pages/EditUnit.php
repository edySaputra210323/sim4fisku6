<?php

namespace App\Filament\Admin\Clusters\Master\Resources\UnitResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
