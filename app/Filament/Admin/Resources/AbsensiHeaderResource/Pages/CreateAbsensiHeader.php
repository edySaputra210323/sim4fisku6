<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use App\Models\AbsensiDetail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Admin\Resources\AbsensiHeaderResource;
use Illuminate\Support\Facades\Auth;

class CreateAbsensiHeader extends CreateRecord
{
    protected static string $resource = AbsensiHeaderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
    $user = auth()->user();

    $tahunAktif = \App\Models\TahunAjaran::where('status', 1)->first();
    $semesterAktif = \App\Models\Semester::where('status', 1)->first();

    $data['tahun_ajaran_id'] = $tahunAktif?->id;
    $data['semester_id'] = $semesterAktif?->id;

    // 🔹 Jika user login sebagai guru
    if ($user->hasRole('guru')) {
        $pegawai = $user->pegawai; // ambil dari relasi
        if (!$pegawai) {
            $this->notify('danger', 'Akun guru ini belum terhubung dengan data pegawai. Hubungi admin.');
            abort(403, 'Akun guru belum terhubung dengan data pegawai.');
        }

        $data['pegawai_id'] = $pegawai->id;
    }

    return $data;
    }

    protected function afterCreate(): void
    {
        $header = $this->record;

        // Ambil siswa aktif di kelas, tahun ajaran & semester terkait
        $riwayatKelas = RiwayatKelas::query()
            ->where('kelas_id', $header->kelas_id)
            ->where('tahun_ajaran_id', $header->tahun_ajaran_id)
            ->where('semester_id', $header->semester_id)
            ->where(function ($q) {
                $q->where('status_aktif', true)
                  ->orWhere('status_aktif', 1);
            })
            ->get(['id']);

        // Kalau tidak ada siswa aktif
        if ($riwayatKelas->isEmpty()) {
            \Log::warning('Tidak ada siswa aktif ditemukan untuk absensi', [
                'kelas_id' => $header->kelas_id,
                'tahun_ajaran_id' => $header->tahun_ajaran_id,
                'semester_id' => $header->semester_id,
            ]);
            return;
        }

        // Insert massal supaya cepat (hindari loop create per baris)
        $details = $riwayatKelas->map(fn ($r) => [
            'absensi_header_id' => $header->id,
            'riwayat_kelas_id'  => $r->id,
            'status'            => 'hadir', // default
            'created_at'        => now(),
            'updated_at'        => now(),
        ])->toArray();

        AbsensiDetail::insert($details);

        // Refresh record agar RelationManager langsung memuat data baru
        $this->record->refresh();
    }

    /**
     * Redirect langsung ke halaman edit agar RelationManager tampil.
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
