<?php

namespace App\Filament\Admin\Clusters\Master\Resources\PenghasilanOrtuResource\Pages;

use App\Filament\Admin\Clusters\Master\Resources\PenghasilanOrtuResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPenghasilanOrtu extends ViewRecord
{
    protected static string $resource = PenghasilanOrtuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
