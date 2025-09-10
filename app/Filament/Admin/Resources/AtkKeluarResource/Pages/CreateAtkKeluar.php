<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;

use App\Filament\Admin\Resources\AtkKeluarResource;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class CreateAtkKeluar extends CreateRecord
{
    protected static string $resource = AtkKeluarResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $activeTahunAjaran = TahunAjaran::where('status', true)->first();
        $activeSemester = Semester::where('status', true)->first();

        if (!$activeTahunAjaran || !$activeSemester) {
            // Lempar error supaya transaksi gagal
            Notification::make()
                ->title('Tahun Ajaran / Semester belum aktif')
                ->danger()
                ->body('Silakan set Tahun Ajaran dan Semester yang aktif terlebih dahulu.')
                ->send();

            // Ini akan menghentikan proses simpan
            throw ValidationException::withMessages([
                'tahun_ajaran_id' => 'Tahun ajaran belum aktif',
                'semester_id' => 'Semester belum aktif',
            ]);
        }

        $data['tahun_ajaran_id'] = $activeTahunAjaran->id;
        $data['semester_id'] = $activeSemester->id;
        $data['ditambah_oleh_id'] = Auth::id();

        // Jika user bukan admin_atk, isi otomatis pegawai_id
        if (!Auth::user()->hasRole('admin_atk')) {
            $data['pegawai_id'] = Auth::user()->pegawai?->id;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->record->applyStatus($data['status'], $data['alasan_batal'] ?? null);
    
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // setelah berhasil simpan transaksi → redirect ke invoice (ViewAtkKeluar)
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
