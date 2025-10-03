<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsensiDetailRelationManager extends RelationManager
{
    protected static string $relationship = 'absensiDetail';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            // Status absensi
            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'hadir' => 'Hadir',
                    'sakit' => 'Sakit',
                    'izin'  => 'Izin',
                    'alpa'  => 'Alpa',
                ])
                ->required(),

            // Keterangan tambahan
            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->nullable(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            // ->recordTitleAttribute('riwayatKelas')
            ->columns([
                // Nama siswa lewat relasi riwayatKelas → dataSiswa
                Tables\Columns\TextColumn::make('riwayatKelas.dataSiswa.nama_siswa')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),

                // Status absensi (editable langsung di tabel)
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin'  => 'Izin',
                        'alpa'  => 'Alpa',
                    ])
                    ->rules(['required']),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
