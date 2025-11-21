<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Widgets;

use App\Models\Kelas;
use App\Models\AbsensiHeader;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatusAbsensiWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = now()->toDateString();

        // Ambil ID kelas yang sudah melakukan absensi hari ini
        $kelasSudahAbsen = AbsensiHeader::whereDate('tanggal', $today)
            ->pluck('kelas_id')
            ->toArray();

        // Ambil daftar kelas yang belum melakukan absensi
        $kelasBelumAbsen = Kelas::whereNotIn('id', $kelasSudahAbsen)->get();

        if ($kelasBelumAbsen->isEmpty()) {
            return [
                Stat::make('Status Absensi Hari Ini', '✅ Semua kelas sudah melakukan absensi.')
                    ->color('success')
                    ->description('Tidak ada kelas yang tertinggal.'),
            ];
        }

        return [
            Stat::make(
                'Kelas Belum Melakukan Absensi Hari Ini',
                $kelasBelumAbsen->pluck('nama_kelas')->join(', ')
            )
                ->color('danger')
                ->description('Segera lakukan absensi harian untuk kelas tersebut.'),
        ];
    }
}
