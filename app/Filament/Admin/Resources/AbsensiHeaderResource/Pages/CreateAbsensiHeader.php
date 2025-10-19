<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use App\Models\AbsensiDetail;
use App\Models\AbsensiHeader;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\AbsensiHeaderResource;

class CreateAbsensiHeader extends CreateRecord
{
    protected static string $resource = AbsensiHeaderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        $tahunAktif = TahunAjaran::where('status', 1)->first();
        $semesterAktif = Semester::where('status', 1)->first();

        $data['tahun_ajaran_id'] = $tahunAktif?->id;
        $data['semester_id'] = $semesterAktif?->id;

    return $data;
    }

    protected function beforeCreate(): void
    {
        $data = $this->form->getState();

        $tahunAktif = \App\Models\TahunAjaran::where('status', 1)->first();
        $semesterAktif = \App\Models\Semester::where('status', 1)->first();

        $exists = \App\Models\AbsensiHeader::where('kelas_id', $data['kelas_id'])
            ->whereDate('tanggal', $data['tanggal'])
            ->where('tahun_ajaran_id', $tahunAktif?->id)
            ->where('semester_id', $semesterAktif?->id)
            ->exists();

        if ($exists) {
            \Filament\Notifications\Notification::make()
                ->danger()
                ->title('Data absensi sudah ada')
                ->body('Absensi untuk kelas ini pada tanggal tersebut sudah dibuat sebelumnya.')
                ->send();

            $this->halt(); // hentikan proses create agar tidak error SQL
        }
    }

    protected function afterCreate(): void
    {
        $header = $this->record;

        $riwayatKelas = RiwayatKelas::query()
            ->where('kelas_id', $header->kelas_id)
            ->where('tahun_ajaran_id', $header->tahun_ajaran_id)
            ->where('semester_id', $header->semester_id)
            ->where(function ($q) {
                $q->where('status_aktif', true)->orWhere('status_aktif', 1);
            })
            ->get(['id']);

        if ($riwayatKelas->isEmpty()) {
            \Log::warning('Tidak ada siswa aktif ditemukan untuk absensi', [
                'kelas_id' => $header->kelas_id,
                'tahun_ajaran_id' => $header->tahun_ajaran_id,
                'semester_id' => $header->semester_id,
            ]);
            return;
        }

        $details = $riwayatKelas->map(fn($r) => [
            'absensi_header_id' => $header->id,
            'riwayat_kelas_id'  => $r->id,
            'status'            => 'hadir',
            'created_at'        => now(),
            'updated_at'        => now(),
        ])->toArray();

        AbsensiDetail::insert($details);

        $this->record->refresh();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Absensi berhasil dibuat';
    }
}
