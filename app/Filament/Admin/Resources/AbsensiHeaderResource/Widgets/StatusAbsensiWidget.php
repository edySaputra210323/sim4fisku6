<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Widgets;

use Filament\Widgets\Widget;
use App\Models\AbsensiHeader;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class StatusAbsensiWidget extends Widget
{
    protected int|string|array $columnSpan = 'full';
    
    protected static string $view = 'filament.admin.resources.absensi-header-resource.widgets.status-absensi-widget';

    protected function getViewData(): array
    {
        $today = now()->toDateString();

        // Total kelas
        $totalKelas = Kelas::count();

        // Kelas yang sudah absensi hari ini
        $kelasSudahAbsensiIds = AbsensiHeader::whereDate('tanggal', $today)
            ->pluck('kelas_id')
            ->unique()
            ->toArray();

        // Kelas yang belum absensi
        $kelasBelumAbsensi = Kelas::whereNotIn('id', $kelasSudahAbsensiIds)
            ->pluck('nama_kelas')
            ->toArray();

        $jumlahSudahAbsensi = count($kelasSudahAbsensiIds);
        $jumlahBelumAbsensi = count($kelasBelumAbsensi);

        $persentase = $totalKelas > 0
            ? round(($jumlahSudahAbsensi / $totalKelas) * 100)
            : 0;

        return [
            'today' => now()->locale('id')->translatedFormat('l, d F Y'),
            'totalKelas' => $totalKelas,
            'sudahAbsensi' => $jumlahSudahAbsensi,
            'belumAbsensi' => $jumlahBelumAbsensi,
            'persentase' => $persentase,
            'kelasBelumAbsensi' => $kelasBelumAbsensi,
        ];
    }
}
