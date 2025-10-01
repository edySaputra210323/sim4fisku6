<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Atk;
use App\Models\DetailAtkKeluar;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class LaporanAtk extends Page
{
    use HasPageShield;

    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.laporan-atk';

    protected static ?string $navigationGroup = 'ATK';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Laporan ATK';

    public function getHeading(): string
    {
        return 'Laporan ATK';
    }

    /**
     * Barang habis (stok 0 atau kurang)
     */
    public function getBarangHabis()
    {
        return Atk::where('stock', '<=', 0)->get();
    }

    /**
     * Barang masuk (sementara dari tabel ATK)
     */
    public function getBarangMasuk()
    {
        return Atk::orderByDesc('created_at')->limit(10)->get();
    }

    /**
     * Cek apakah ada tahun ajaran & semester aktif
     */
    protected function hasActivePeriod(): bool
    {
        return TahunAjaran::where('status', true)->exists()
            && Semester::where('status', true)->exists();
    }

    /**
     * Query dasar untuk filter tahun ajaran & semester aktif
     */
    protected function baseQuery()
    {
        if (! $this->hasActivePeriod()) {
            return DetailAtkKeluar::whereRaw('1=0'); // return kosong
        }

        return DetailAtkKeluar::whereHas('atkKeluar.tahunAjaran', fn ($q) => $q->where('status', true))
            ->whereHas('atkKeluar.semester', fn ($q) => $q->where('status', true));
    }

    /**
     * Barang keluar terbaru
     */
    public function getBarangKeluar()
    {
        return $this->baseQuery()
            ->with('atk')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    /**
     * Top 5 ATK paling sering diambil
     */
    public function getTopAtk()
    {
        return $this->baseQuery()
            ->select('atk_id', DB::raw('SUM(qty) as total_keluar'))
            ->with('atk')
            ->groupBy('atk_id')
            ->orderByDesc('total_keluar')
            ->limit(5)
            ->get();
    }
}
