<?php

namespace App\Filament\Admin\Resources\AtkResource\Pages;

use App\Filament\Admin\Resources\AtkResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAtk extends ViewRecord
{
    protected static string $resource = AtkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
