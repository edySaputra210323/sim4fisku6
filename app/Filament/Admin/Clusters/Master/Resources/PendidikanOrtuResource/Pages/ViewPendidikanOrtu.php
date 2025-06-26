<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PendidikanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPendidikanOrtu extends ViewRecord
{
    protected static string $resource = PendidikanOrtuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
