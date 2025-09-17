<?php

namespace App\Filament\Admin\Resources\AtkResource\Pages;

use App\Filament\Admin\Resources\AtkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAtk extends CreateRecord
{
    protected static string $resource = AtkResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
