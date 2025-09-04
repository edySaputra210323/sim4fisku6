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
    // Ambil Tahun Ajaran aktif
    $activeTahunAjaran = cache()->remember(
        'active_tahun_ajaran',
        now()->addMinutes(1),
        fn () => \App\Models\TahunAjaran::where('status', true)->first()
    );

    if (!$activeTahunAjaran) {
        \Filament\Notifications\Notification::make()
            ->title('Error')
            ->body('Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan terlebih dahulu.')
            ->danger()
            ->send();
        throw new \Exception('No active Tahun Ajaran found.');
    }

    // Ambil Semester aktif
    $activeSemester = cache()->remember(
        'active_semester',
        now()->addMinutes(1),
        fn () => \App\Models\Semester::where('status', true)->first()
    );

    if (!$activeSemester) {
        \Filament\Notifications\Notification::make()
            ->title('Error')
            ->body('Tidak ada Semester yang aktif. Silakan aktifkan terlebih dahulu.')
            ->danger()
            ->send();
        throw new \Exception('No active Semester found.');
    }

    // Inject field otomatis ke record AtkMasuk (bukan ke detail)
    $data['tahun_ajaran_id'] = $activeTahunAjaran->id;
    $data['semester_id'] = $activeSemester->id;
    $data['ditambah_oleh_id'] = auth()->id();

    return $data;
}
}
