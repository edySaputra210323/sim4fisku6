<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DetailAtkKeluar;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TopAtkChart extends ApexChartWidget
{
    protected static ?string $chartId = 'topAtkChart';

    protected static ?string $heading = 'Barang Paling Sering Diambil';

    protected function getOptions(): array
    {
        // ambil data top 5
        $topAtk = DetailAtkKeluar::select('atk_id', DB::raw('SUM(qty) as total_keluar'))
            ->with('atk')
            ->groupBy('atk_id')
            ->orderByDesc('total_keluar')
            ->limit(10)
            ->get();

        $labels = $topAtk->pluck('atk.nama_atk')->toArray();
        $data   = $topAtk->pluck('total_keluar')->toArray();

        return [
            'chart' => [
                'type'   => 'bar',
                'height' => 150,
            ],
            'series' => [
                [
                    'name' => 'Jumlah Keluar',
                    'data' => $data,
                ],
            ],
            'xaxis' => [
                'categories' => $labels,
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
            'colors' => ['#3b82f6'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'horizontal'   => true,
                ],
            ],
        ];
    }
}
