<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

class AbsensiDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'absensiDetails';

    protected static ?string $title = 'Daftar Kehadiran Siswa';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->label('Status Kehadiran')
                ->options([
                    'hadir' => 'Hadir',
                    'sakit' => 'Sakit',
                    'izin'  => 'Izin',
                    'alpa'  => 'Alpa',
                ])
                ->required(),

            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(2)
                ->maxLength(255)
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                        // Kolom nomor urut
            Tables\Columns\TextColumn::make('index')
                ->label('No')
                ->width('1%')
                ->alignCenter()
                ->state(function ($livewire, stdClass $rowLoop): string {
                    // $livewire adalah instance RelationManager yang mengimplement HasTable
                    $perPage = intval(method_exists($livewire, 'getTableRecordsPerPage') ? $livewire->getTableRecordsPerPage() : 0);
                    $page = intval(method_exists($livewire, 'getTablePage') ? $livewire->getTablePage() : 1);
                    $iteration = intval($rowLoop->iteration ?? 0);

                    if ($perPage <= 0) {
                        return (string) $iteration;
                    }

                    return (string) (
                        $iteration + ($perPage * ($page - 1))
                    );
                }),
            Tables\Columns\TextColumn::make('riwayatKelas.dataSiswa.nama_siswa')
                ->label('Nama Siswa')
                ->sortable()
                ->searchable()
                ->wrap(),

            Tables\Columns\SelectColumn::make('status')
                ->label('Status')
                ->options([
                    'hadir' => 'Hadir',
                    'sakit' => 'Sakit',
                    'izin'  => 'Izin',
                    'alpa'  => 'Alpa',
                ])
                ->rules(['required']),
            Tables\Columns\TextInputColumn::make('keterangan')
                ->label('Keterangan')
                ->placeholder('-')
                ->sortable()
                ->searchable(),
            ])
                ->headerActions([]) // absensi detail di-generate otomatis, tidak perlu tambah manual
                ->actions([]) // tidak perlu Edit/Delete manual
                ->bulkActions([]) // tidak perlu hapus massal
                ->striped()
                ->paginated(false); // tampilkan semua siswa sekaligus
    }
}
