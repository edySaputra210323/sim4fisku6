<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;

use App\Filament\Admin\Resources\AtkKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use App\Models\TahunAjaran;
use App\Models\Semester;

class ListAtkKeluars extends ListRecords
{
    protected static string $resource = AtkKeluarResource::class;

    public function mount(): void
    {
        parent::mount();

        $activeTahunAjaran = cache()->remember(
            'active_th_ajaran',
            now()->addMinutes(1),
            fn () => TahunAjaran::where('status', true)->first()
        );

        $activeSemester = cache()->remember(
            'active_semester',
            now()->addMinutes(1),
            fn () => Semester::where('status', true)->first()
        );

        // tampilkan notifikasi sekali per session (mencegah berulang akibat polling)
        if ((! $activeTahunAjaran || ! $activeSemester) && ! session()->has('atk_periode_warning_shown')) {
            session()->put('atk_periode_warning_shown', true);

            if (! $activeTahunAjaran && ! $activeSemester) {
                $message = 'Tahun ajaran dan semester belum diaktifkan. Silakan aktifkan terlebih dahulu.';
            } elseif (! $activeTahunAjaran) {
                $message = 'Tahun ajaran belum diaktifkan. Silakan aktifkan terlebih dahulu.';
            } elseif (! $activeSemester) {
                $message = 'Semester belum diaktifkan. Silakan aktifkan terlebih dahulu.';
            }

            Notification::make()
                ->title('Peringatan')
                ->body($message)
                ->warning()
                ->persistent()
                ->send();
        }
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
