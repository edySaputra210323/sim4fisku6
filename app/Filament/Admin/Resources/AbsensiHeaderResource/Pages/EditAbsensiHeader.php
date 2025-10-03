<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;

use App\Filament\Admin\Resources\AbsensiHeaderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiHeader extends EditRecord
{
    protected static string $resource = AbsensiHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
