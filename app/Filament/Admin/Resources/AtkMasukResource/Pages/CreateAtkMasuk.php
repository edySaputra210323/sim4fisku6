<?php

namespace App\Filament\Admin\Resources\AtkMasukResource\Pages;

use Filament\Actions;
use App\Models\AtkMasuk;
use Filament\Notifications\Notification;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\AtkMasukResource;

class CreateAtkMasuk extends CreateRecord
{
    protected static string $resource = AtkMasukResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Mengambil tahun ajaran yang aktif
        $activeTahunAjaran = cache()->remember('active_tahun_ajaran', now()->addMinutes(1), fn () => TahunAjaran::where('status', true)->first());
        if (!$activeTahunAjaran) {
            Notification::make()
                ->title('Error')
                ->body('Tidak ada tahun ajaran yang aktif. Silakan aktifkan tahun ajaran terlebih dahulu.')
                ->danger()
                ->send();
            throw new \Exception('No active Tahun Ajaran found.');
        }

        // Mengambil semester yang aktif
        $activeSemester = cache()->remember('active_semester', now()->addMinutes(1), fn () => Semester::where('status', true)->first());
        if (!$activeSemester) {
            Notification::make()
                ->title('Error')
                ->body('Tidak ada semester yang aktif. Silakan aktifkan semester terlebih dahulu.')
                ->danger()
                ->send();
            throw new \Exception('No active Semester found.');
        }

        // Simpan setiap item sebagai record AtkMasuk
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
}
