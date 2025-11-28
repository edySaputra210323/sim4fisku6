<?php

namespace App\Filament\Admin\Resources\JurnalGuruResource\Pages;

use Filament\Forms;
use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\RekapJurnalGuruExport;
use App\Exports\RekapJurnalGuruExport;
use App\Exports\RekapJurnalKelasExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Admin\Resources\JurnalGuruResource;

class ListJurnalGurus extends ListRecords
{
    protected static string $resource = JurnalGuruResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Buat Jurnal')
            ->icon('heroicon-o-plus')
            ->color('primary'),
            // Export Jurnal Kelas
Actions\Action::make('exportJurnalKelas')
    ->label('Export Jurnal Kelas')
    ->icon('heroicon-o-document-arrow-down')
    ->color('warning')
    ->form([
        Forms\Components\DatePicker::make('start_date')
            ->label('Dari Tanggal')
            ->required(),

        Forms\Components\DatePicker::make('end_date')
            ->label('Sampai Tanggal')
            ->required(),

        Forms\Components\Select::make('kelas_id')
            ->label('Pilih Kelas')
            ->relationship('kelas', 'nama_kelas')
            ->preload()
            ->searchable()
            ->required(),
    ])
    ->action(function (array $data) {
        return Excel::download(
            new RekapJurnalKelasExport(
                $data['start_date'],
                $data['end_date'],
                $data['kelas_id'],
            ),
            'rekap_jurnal_kelas_' . now()->format('Ymd_His') . '.xlsx'
        );
    }),
            // ✅ Tambahkan Export Jurnal Guru di sini
            Actions\Action::make('exportJurnalGuru')
                ->label('Export Jurnal Mengajar')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal')
                        ->required(),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal')
                        ->required(),

                    Forms\Components\Select::make('guru_id')
                        ->label('Guru (Opsional)')
                        ->relationship('guru', 'nm_pegawai')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('kelas_id')
                        ->label('Kelas (Opsional)')
                        ->relationship('kelas', 'nama_kelas')
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('mapel_id')
                        ->label('Mapel (Opsional)')
                        ->relationship('mapel', 'nama_mapel')
                        ->searchable()
                        ->preload(),
                ])
                ->action(function (array $data) {
                    return Excel::download(
                        new RekapJurnalGuruExport(
                            $data['start_date'],
                            $data['end_date'],
                            $data['guru_id'] ?? null,
                            $data['kelas_id'] ?? null,
                            $data['mapel_id'] ?? null,
                        ),
                        'rekap_jurnal_guru_' . now()->format('Ymd_His') . '.xlsx'
                    );
                }),

        ];
    }
}
