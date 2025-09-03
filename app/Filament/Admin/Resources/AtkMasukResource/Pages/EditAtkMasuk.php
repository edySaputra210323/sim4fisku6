<?php

namespace App\Filament\Admin\Resources\AtkMasukResource\Pages;

use App\Filament\Admin\Resources\AtkMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\AtkMasuk;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Notifications\Notification;
class EditAtkMasuk extends EditRecord
{
    protected static string $resource = AtkMasukResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Untuk edit, ambil semua record dengan nomor nota yang sama
        $records = AtkMasuk::where('nota', $this->record->nota)->get();
        $data['items'] = $records->map(fn ($record) => [
            'atk_id' => $record->atk_id,
            'qty' => $record->qty,
            'harga_satuan' => $record->harga_satuan,
            'total_harga' => $record->total_harga,
        ])->toArray();
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Untuk edit, hapus semua record lama dengan nomor nota yang sama
        $oldRecords = AtkMasuk::where('nota', $this->record->nota)->get();
        foreach ($oldRecords as $oldRecord) {
            $oldRecord->delete(); // Soft delete, akan memicu pengurangan stok
        }

        // Simpan record baru dari Repeater
        $activeTahunAjaran = cache()->remember('active_tahun_ajaran', now()->addMinutes(1), fn () => TahunAjaran::where('status', true)->first());
        $activeSemester = cache()->remember('active_semester', now()->addMinutes(1), fn () => Semester::where('status', true)->first());

        if (!$activeTahunAjaran || !$activeSemester) {
            Notification::make()
                ->title('Error')
                ->body('Tahun ajaran atau semester aktif tidak ditemukan.')
                ->danger()
                ->send();
            throw new \Exception('No active Tahun Ajaran or Semester found.');
        }

        foreach ($data['items'] ?? [] as $item) {
            AtkMasuk::create([
                'atk_id' => $item['atk_id'] ?? null,
                'qty' => $item['qty'] ?? 0,
                'harga_satuan' => $item['harga_satuan'] ?? 0,
                'total_harga' => ($item['qty'] ?? 0) * ($item['harga_satuan'] ?? 0),
                'nota' => $data['nota'] ?? null,
                'tahun_ajaran_id' => $activeTahunAjaran->id,
                'semester_id' => $activeSemester->id,
                'tanggal' => $data['tanggal'] ?? now(),
                'foto_nota' => $data['foto_nota'] ?? null,
                'ditambah_oleh_id' => auth()->id(),
            ]);
        }

        // Tidak menyimpan record AtkMasuk langsung dari form
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
