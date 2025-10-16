<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
