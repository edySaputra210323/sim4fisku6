<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Atk;
use App\Models\DetailAtkKeluar;
use Illuminate\Support\Facades\DB;

class LaporanAtk extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.laporan-atk';

    protected static ?string $navigationGroup = 'ATK';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Laporan ATK';


    public function getHeading(): string
    {
        return 'Laporan ATK';
    }

    public function getBarangHabis()
    {
        return Atk::where('stock', '<=', 0)->get();
    }

    public function getBarangMasuk()
    {
        return Atk::orderByDesc('created_at')->limit(10)->get(); // contoh, kalau ada tabel khusus barang masuk lebih bagus
    }

    public function getBarangKeluar()
    {
        return DetailAtkKeluar::with('atk')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    public function getTopAtk()
    {
        return DetailAtkKeluar::select('atk_id', DB::raw('SUM(qty) as total_keluar'))
            ->with('atk')
            ->groupBy('atk_id')
            ->orderByDesc('total_keluar')
            ->limit(5)
            ->get();
    }
}
