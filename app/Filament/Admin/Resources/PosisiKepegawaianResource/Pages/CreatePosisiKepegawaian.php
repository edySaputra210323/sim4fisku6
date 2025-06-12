<?php

namespace App\Filament\Admin\Resources\PosisiKepegawaianResource\Pages;

use App\Filament\Admin\Resources\PosisiKepegawaianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePosisiKepegawaian extends CreateRecord
{
    protected static string $resource = PosisiKepegawaianResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
