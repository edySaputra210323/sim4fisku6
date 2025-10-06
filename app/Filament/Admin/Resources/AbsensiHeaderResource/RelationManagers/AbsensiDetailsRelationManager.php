<?php

namespace App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsensiDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'absensiDetails';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin'  => 'Izin',
                        'alpa'  => 'Alpa',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_siswa')
            ->columns([
                 // ambil nama siswa dari relasi
                 Tables\Columns\TextColumn::make('riwayatKelas.dataSiswa.nama_siswa')
                 ->label('Nama Siswa')
                 ->sortable()
                 ->searchable(),

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
                // Tables\Actions\CreateAction::make(),
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
