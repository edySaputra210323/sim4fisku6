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
            Actions\Action::make('selesai')
                ->label('Selesai Absensi')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    // optional: $this->record->update(['status' => 'selesai']);
                    return redirect(AbsensiHeaderResource::getUrl('index'));
                    // atau jika berada di Page class: return redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}
