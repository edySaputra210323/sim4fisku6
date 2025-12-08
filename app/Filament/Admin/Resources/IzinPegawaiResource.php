<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\IzinPegawaiResource\Pages;
use App\Filament\Admin\Resources\IzinPegawaiResource\RelationManagers;
use App\Models\IzinPegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IzinPegawaiResource extends Resource
{
    protected static ?string $model = IzinPegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pegawai_id')
                    ->label('Pegawai')
                    ->relationship('pegawai', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('jenis_izin_id')
                    ->label('Jenis Izin')
                    ->relationship('jenisIzinPegawai', 'nama')
                    ->searchable()
                    ->required(),

                Forms\Components\Textarea::make('alasan')
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('jam')
                    ->label('Durasi/Range Jam')
                    ->required(),

                Forms\Components\Select::make('status')
                    ->disabled()
                    ->options([
                        'draft' => 'Draft',
                        'diajukan' => 'Diajukan',
                        'proses_kepala' => 'Proses Kepala Sekolah',
                        'proses_sdm' => 'Proses SDM',
                        'disetujui' => 'Disetujui',
                        'revisi_pegawai' => 'Revisi Pegawai',
                        'ditolak' => 'Ditolak',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_izin_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_kepala_sekolah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_sdm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIzinPegawais::route('/'),
            'create' => Pages\CreateIzinPegawai::route('/create'),
            'view' => Pages\ViewIzinPegawai::route('/{record}'),
            'edit' => Pages\EditIzinPegawai::route('/{record}/edit'),
        ];
    }
}
