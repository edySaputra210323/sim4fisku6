<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;

use Filament\Forms;
use App\Models\Kelas;
use App\Models\Mapel;
use Filament\Actions;
use App\Models\Pegawai;
use App\Models\Semester;
// use Filament\Tables\Actions;
use App\Models\TahunAjaran;
use App\Exports\RekapAbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapJurnalGuruExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\AbsensiHeaderResource;

class ListAbsensiHeaders extends ListRecords
{
    protected static string $resource = AbsensiHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

           Actions\Action::make('exportAbsensi')
            ->label('Export Absensi')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->form([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->required(),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->required(),

                // Ganti relationship() menjadi options()
                Forms\Components\Select::make('guru_id')
                    ->label('Guru (Opsional)')
                    ->options(fn () => Pegawai::orderBy('nm_pegawai')->pluck('nm_pegawai','id')->toArray())
                    ->searchable()
                    ->placeholder('Semua Guru'),

                Forms\Components\Select::make('kelas_id')
                    ->label('Kelas (Opsional)')
                    ->options(fn () => Kelas::orderBy('nama_kelas')->pluck('nama_kelas','id')->toArray())
                    ->searchable()
                    ->placeholder('Semua Kelas'),

                Forms\Components\Select::make('mapel_id')
                    ->label('Mapel (Opsional)')
                    ->options(fn () => Mapel::orderBy('nama_mapel')->pluck('nama_mapel','id')->toArray())
                    ->searchable()
                    ->placeholder('Semua Mapel'),

                Forms\Components\Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran (Opsional)')
                    ->options(fn () => TahunAjaran::orderByDesc('th_ajaran')->pluck('th_ajaran','id')->toArray())
                    ->searchable()
                    ->placeholder('Semua Tahun Ajaran'),

                Forms\Components\Select::make('semester_id')
                    ->label('Semester (Opsional)')
                    ->options(fn () => Semester::orderBy('id')->pluck('nm_semester','id')->toArray())
                    ->searchable()
                    ->placeholder('Semua Semester'),
            ])
            ->action(function (array $data) {
                $export = new \App\Exports\RekapAbsensiExport(
                    $data['start_date'],
                    $data['end_date'],
                    $data['guru_id'] ?? null,
                    $data['kelas_id'] ?? null,
                    $data['mapel_id'] ?? null,
                    $data['tahun_ajaran_id'] ?? null,
                    $data['semester_id'] ?? null,
                );

                // jangan panggil Maatwebsite statis, inject atau panggil facade instance
                return app(\Maatwebsite\Excel\Excel::class)
                    ->download($export, 'rekap_absensi_' . now()->format('Ymd_His') . '.xlsx');
            }),
        ];
    }
}
