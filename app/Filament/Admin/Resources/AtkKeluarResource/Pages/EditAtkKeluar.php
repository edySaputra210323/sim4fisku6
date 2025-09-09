<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;

use App\Filament\Admin\Resources\AtkKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAtkKeluar extends EditRecord
{
    protected static string $resource = AtkKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // 🔹 Kalau status diverifikasi
        if ($data['status'] === 'verified') {
            $data['verified_by_id'] = auth()->id();
            $data['verified_at'] = now();
        }

        // 🔹 Kalau status dibatalkan
        if ($data['status'] === 'canceled') {
            // Pastikan detail + relasi atk diload
            $this->record->loadMissing('details.atk');

            foreach ($this->record->details as $detail) {
                if ($detail->atk) {
                    // rollback stok
                    $detail->atk->increment('stock', $detail->qty);
                }
            }

            $data['canceled_by_id'] = auth()->id();
            $data['canceled_at'] = now();
        }

        return $data;
    }
}
