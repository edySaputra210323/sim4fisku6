<?php

namespace App\Filament\Admin\Resources\SuratMasukResource\Pages;

use App\Filament\Admin\Resources\SuratMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratMasuk extends EditRecord
{
    protected static string $resource = SuratMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
