<?php

namespace App\Filament\Admin\Resources\RiwayatKelasResource\Pages;

use App\Models\Kelas;
use Filament\Actions;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Support\Str;
use App\Models\RiwayatKelas;
use App\Exports\RiwayatKelasExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Admin\Resources\RiwayatKelasResource;
use App\Filament\Admin\Resources\RiwayatKelasResource\Widgets\RiwayatKelasWidgets;

class ListRiwayatKelas extends ListRecords
{
    protected static string $resource = RiwayatKelasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Ekspor Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $activeTahunAjaran = TahunAjaran::where('status', true)->first();
                    $activeSemester = $activeTahunAjaran
                        ? Semester::where('th_ajaran_id', $activeTahunAjaran->id)->where('status', true)->first()
                        : null;

                    if (!$activeTahunAjaran || !$activeSemester) {
                        Notification::make()
                            ->title('Peringatan')
                            ->body('Tahun ajaran atau semester aktif tidak ditemukan.')
                            ->warning()
                            ->send();
                        return;
                    }

                    $activeTab = $this->activeTab ?? 'all';
                    $kelasId = $activeTab !== 'all' ? $activeTab : null;

                    $tahunAjaran = TahunAjaran::where('status', true)->first();
                    $semester = Semester::where('th_ajaran_id', $tahunAjaran->id)->where('status', true)->first();

                     // Sanitasi nama file
                     $tahunAjaranSafe = Str::slug($tahunAjaran->th_ajaran, '-'); // Mengganti / atau spasi dengan -
                     $semesterSafe = Str::slug($semester->nm_semester, '-'); // Mengganti spasi atau karakter lain dengan -

                     $fileName = $activeTab === 'all'
                        ? "Data_Siswa_Semua_Kelas_{$tahunAjaranSafe}_{$semesterSafe}.xlsx"
                        : "Data_Siswa_Kelas_" . Str::slug(Kelas::find($activeTab)->nama_kelas, '-') . "_{$tahunAjaranSafe}_{$semesterSafe}.xlsx";

                    // $fileName = $activeTab === 'all'
                    //     ? "Data_Siswa_Semua_Kelas_{$tahunAjaran->th_ajaran}_{$semester->nm_semester}.xlsx"
                    //     : "Data_Siswa_Kelas_" . Kelas::find($activeTab)->nama_kelas . "_{$tahunAjaran->th_ajaran}_{$semester->nm_semester}.xlsx";

                    return Excel::download(
                        new RiwayatKelasExport($kelasId, $tahunAjaran->id, $semester->id),
                        $fileName
                    );
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        // Ambil tahun ajaran aktif
        $activeTahunAjaran = TahunAjaran::where('status', true)->first();
        if (!$activeTahunAjaran) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada tahun ajaran aktif. Aktifkan tahun ajaran terlebih dahulu.')
                ->warning()
                ->persistent()
                ->send();
            return [
                'all' => Tab::make('Semua Kelas')
                    ->badge(0)
                    ->badgeColor('primary'),
            ];
        }

        // Ambil semester aktif untuk tahun ajaran aktif
        $activeSemester = Semester::where('th_ajaran_id', $activeTahunAjaran->id)
            ->where('status', true)
            ->first();
        if (!$activeSemester) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada semester aktif untuk tahun ajaran ini. Aktifkan semester terlebih dahulu.')
                ->warning()
                ->persistent()
                ->send();
            return [
                'all' => Tab::make('Semua Kelas')
                    ->badge(0)
                    ->badgeColor('primary'),
            ];
        }

        // Ambil semua kelas dari tabel kelas
        $kelas = Kelas::pluck('nama_kelas', 'id')->toArray();

        // Jika tidak ada data kelas, tampilkan notifikasi
        if (empty($kelas)) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada data kelas tersedia. Tambahkan kelas terlebih dahulu.')
                ->warning()
                ->send();
            return [
                'all' => Tab::make('Semua Kelas')
                    ->badge(RiwayatKelas::where('tahun_ajaran_id', $activeTahunAjaran->id)
                        ->where('semester_id', $activeSemester->id)
                        ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
                        ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
                        ->where('status_siswa.status', 'aktif')
                        ->count())
                    ->badgeColor('primary'),
            ];
        }

        // Buat tab dinamis berdasarkan data kelas
        $tabs = [
            'all' => Tab::make('Semua Kelas')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                    ->where('semester_id', $activeSemester->id)
                    ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
                    ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
                    ->where('status_siswa.status', 'aktif'))
                ->badge(RiwayatKelas::where('tahun_ajaran_id', $activeTahunAjaran->id)
                    ->where('semester_id', $activeSemester->id)
                    ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
                    ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
                    ->where('status_siswa.status', 'aktif')
                    ->count())
                ->badgeColor('primary'),
        ];

        foreach ($kelas as $id => $nama_kelas) {
            $tabs[$id] = Tab::make($nama_kelas)
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('riwayat_kelas.kelas_id', $id)
                    ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                    ->where('semester_id', $activeSemester->id)
                    ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
                    ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
                    ->where('status_siswa.status', 'aktif'))
                ->badge(RiwayatKelas::where('riwayat_kelas.kelas_id', $id)
                    ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                    ->where('semester_id', $activeSemester->id)
                    ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
                    ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
                    ->where('status_siswa.status', 'aktif')
                    ->count())
                ->badgeColor('success');
        }

        return $tabs;
    }
}
