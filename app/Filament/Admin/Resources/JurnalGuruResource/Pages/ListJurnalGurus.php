<?php

namespace App\Filament\Admin\Resources\JurnalGuruResource\Pages;

use Filament\Forms;
use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapJurnalGuruExport;
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
            // ✅ Tambahkan Export Jurnal Kelas
            Actions\Action::make('#')
                ->label('Export Jurnal Kelas')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning'),
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
                        new \App\Exports\RekapJurnalGuruExport(
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
