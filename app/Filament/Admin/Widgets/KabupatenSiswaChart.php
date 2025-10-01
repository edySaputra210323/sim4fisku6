<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DataSiswa;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class KabupatenSiswaChart extends ApexChartWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $chartId = 'kabupatenSiswaChart';
    protected static ?string $heading = 'Jumlah Siswa per Kabupaten';

    // 🚀 Tambahkan ini untuk membatasi akses
    public static function canView(): bool
    {
        $user = auth()->user();

        // hanya tampil kalau bukan role guru
        return $user && ! $user->hasRole('guru');
    }

    protected function getFilters(): ?array
    {
        return DataSiswa::query()
            ->select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan', 'angkatan')
            ->toArray();
    }

    protected function getOptions(): array
    {
        $angkatan = $this->filter ?? DataSiswa::max('angkatan');

        $data = DataSiswa::query()
            ->where('angkatan', $angkatan)
            ->whereNotNull('kabupaten')
            ->selectRaw('kabupaten, COUNT(*) as total')
            ->groupBy('kabupaten')
            ->orderByDesc('total') // urutkan dari yang terbanyak
            ->pluck('total', 'kabupaten')
            ->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => "Jumlah Siswa ($angkatan)",
                    'data' => array_values($data),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($data),
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 6,
                    'horizontal' => false,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'colors' => ['#FFFFFF'],
                    'fontSize' => '14px',
                    'fontWeight' => 'bold',
                ],
            ],
        ];
    }
}
