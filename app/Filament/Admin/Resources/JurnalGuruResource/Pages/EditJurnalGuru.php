<?php

namespace App\Filament\Admin\Resources\JurnalGuruResource\Pages;

use App\Filament\Admin\Resources\JurnalGuruResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJurnalGuru extends EditRecord
{
    protected static string $resource = JurnalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
