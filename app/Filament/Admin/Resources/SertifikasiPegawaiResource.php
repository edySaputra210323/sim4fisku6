<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SertifikasiPegawaiResource\Pages;
use App\Filament\Admin\Resources\SertifikasiPegawaiResource\RelationManagers;
use App\Models\SertifikasiPegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SertifikasiPegawaiResource extends Resource
{
    protected static ?string $model = SertifikasiPegawai::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Pengelolaan Pegawai';

    protected static ?string $navigationLabel = 'Sertifikasi Pegawai';

    protected static ?string $modelLabel = 'Sertifikasi Pegawai';

    protected static ?string $pluralModelLabel = 'Sertifikasi Pegawai';

    protected static ?string $slug = 'sertifikasi-pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pegawai_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nm_sertifikasi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('penerbit')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tgl_sertifikasi')
                    ->required(),
                Forms\Components\DatePicker::make('tgl_kadaluarsa'),
                Forms\Components\TextInput::make('no_sertifikat')
                    ->maxLength(255),
                Forms\Components\TextInput::make('file_sertifikat_sertifikasi')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nm_sertifikasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('penerbit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_sertifikasi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_kadaluarsa')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_sertifikat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_sertifikat_sertifikasi')
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
            'index' => Pages\ListSertifikasiPegawais::route('/'),
            'create' => Pages\CreateSertifikasiPegawai::route('/create'),
            'view' => Pages\ViewSertifikasiPegawai::route('/{record}'),
            'edit' => Pages\EditSertifikasiPegawai::route('/{record}/edit'),
        ];
    }
}
