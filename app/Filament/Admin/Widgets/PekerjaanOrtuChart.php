<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DataSiswa;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PekerjaanOrtuChart extends ApexChartWidget
{
    
    protected static ?string $chartId = 'pekerjaanOrtuChart';
    protected static ?string $heading = 'Pekerjaan Orang Tua (Ayah + Ibu)';

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

    protected function getOptions(): array
    {
        // Ambil filter angkatan yang dipilih, default ke terbaru
        $angkatan = $this->filter ?? DataSiswa::max('angkatan');

        // Gabungkan pekerjaan ayah + ibu pakai union
        $data = DataSiswa::query()
            ->where('angkatan', $angkatan)
            ->select('pekerjaan_ayah_id as pekerjaan_id')
            ->unionAll(
                DataSiswa::query()
                    ->where('angkatan', $angkatan)
                    ->select('pekerjaan_ibu_id as pekerjaan_id')
            );

        $result = DB::table(DB::raw("({$data->toSql()}) as u"))
            ->mergeBindings($data->getQuery()) // penting biar binding where() ikut
            ->join('pekerjaan_ortu as p', 'p.id', '=', 'u.pekerjaan_id')
            ->select('p.nama_pekerjaan', DB::raw('COUNT(*) as total'))
            ->groupBy('p.nama_pekerjaan')
            ->pluck('total', 'p.nama_pekerjaan')
            ->toArray();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 350,
            ],
            'series' => array_values($result), // jumlah pekerjaan
            'labels' => array_keys($result),   // nama pekerjaan
        ];
    }
}
