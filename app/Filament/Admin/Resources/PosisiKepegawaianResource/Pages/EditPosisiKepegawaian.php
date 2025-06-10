<?php

namespace App\Filament\Admin\Resources\PosisiKepegawaianResource\Pages;

use App\Filament\Admin\Resources\PosisiKepegawaianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPosisiKepegawaian extends EditRecord
{
    protected static string $resource = PosisiKepegawaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
