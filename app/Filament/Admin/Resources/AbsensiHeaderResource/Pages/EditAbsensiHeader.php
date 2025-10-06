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
                    // opsional: update status header jadi "selesai"
                    // $this->record->update(['status' => 'selesai']);

                    // redirect balik ke list absensi
                    return redirect()->route('filament.admin.resources.absensi-headers.index');
                }),
        ];
    }
}
