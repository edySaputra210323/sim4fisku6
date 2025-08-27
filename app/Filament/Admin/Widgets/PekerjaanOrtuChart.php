<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DataSiswa;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PekerjaanOrtuChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'pekerjaanOrtuChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Pekerjaan Orang Tua/Wali';

    /**
     * Dropdown filter angkatan
     */
    protected function getFilters(): ?array
    {
        return DataSiswa::query()
            ->select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan', 'angkatan')
            ->toArray();
    }

    /**
     * Chart options
     */
    protected function getOptions(): array
    {
        // Ambil filter angkatan yg dipilih (kalau tidak pilih pakai angkatan terbaru)
        $angkatan = $this->filter ?? DataSiswa::max('angkatan');

        // Gabungkan pekerjaan ayah + ibu
        $data = DataSiswa::query()
            ->where('angkatan', $angkatan)
            ->select('pekerjaan_ayah_id as pekerjaan_id')
            ->unionAll(
                DataSiswa::query()
                    ->where('angkatan', $angkatan)
                    ->select('pekerjaan_ibu_id as pekerjaan_id')
            );

        $result = DB::table(DB::raw("({$data->toSql()}) as u"))
            ->mergeBindings($data->getQuery())
            ->join('pekerjaan_ortu as p', 'p.id', '=', 'u.pekerjaan_id')
            ->select('p.nama_pekerjaan', DB::raw('COUNT(*) as total'))
            ->groupBy('p.nama_pekerjaan')
            ->orderByDesc('total')
            ->pluck('total', 'p.nama_pekerjaan')
            ->toArray();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 350,
            ],
            'series' => [
                [
                    'name' => 'Jumlah',
                    'data' => array_values($result),
                ],
            ],
            'xaxis' => [
                'categories' => array_keys($result),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal' => true,
                ],
            ],
        ];
    }
}
