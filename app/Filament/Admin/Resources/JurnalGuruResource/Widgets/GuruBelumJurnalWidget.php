<?php

namespace App\Filament\Admin\Resources\JurnalGuruResource\Widgets;

use App\Models\JadwalMengajar;
use App\Models\JurnalGuru;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class GuruBelumJurnalWidget extends BaseWidget
{
    protected static ?string $heading = 'Guru yang Belum Mengisi Jurnal Hari Ini';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $hariIni = now()->locale('id')->dayName; // "Senin", "Selasa", ...

        return $table
            ->query(
                JadwalMengajar::query()
                    ->with(['guru'])
                    ->whereNotNull('jam_ke')                        // jam_ke wajib ada
                    ->where('jam_ke', '!=', '')                     // jam_ke tidak boleh kosong
                    ->whereHas('guru')                              // wajib ada guru
        ->where('hari', ucfirst($hariIni))              // filter hari

        // cek guru belum buat jurnal hari ini
        ->whereDoesntHave('guru.jurnalGuru', function ($q) {
            $q->whereDate('tanggal', today());
        })
            )
            ->columns([
                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Guru')
                    ->searchable(),

                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->label('Mapel'),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('jam_ke')
                    ->label('Jam Ke')
                    ->formatStateUsing(fn ($state) =>
                        is_array($state) ? implode(', ', $state) : $state
                    )
                    ->badge(),

                Tables\Columns\TextColumn::make('waktu_mengajar')
                    ->label('Waktu'),
            ]);
    }
}
