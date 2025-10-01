<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PendidikanPegawaiResource\Pages;
use App\Filament\Admin\Resources\PendidikanPegawaiResource\RelationManagers;
use App\Models\PendidikanPegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PendidikanPegawaiResource extends Resource
{
    protected static ?string $model = PendidikanPegawai::class;

    // protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Pengelolaan Pegawai';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Pendidikan Pegawai';

    protected static ?string $modelLabel = 'Pendidikan Pegawai';

    protected static ?string $pluralModelLabel = 'Pendidikan Pegawai';

    protected static ?string $slug = 'pendidikan-pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('pegawai_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('level')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('jurusan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('universitas')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tahun_lulus')
                    ->required(),
                Forms\Components\TextInput::make('no_ijazah')
                    ->maxLength(255),
                Forms\Components\TextInput::make('file_ijazah')
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
                Tables\Columns\TextColumn::make('level')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jurusan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('universitas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahun_lulus'),
                Tables\Columns\TextColumn::make('no_ijazah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_ijazah')
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
            'index' => Pages\ListPendidikanPegawais::route('/'),
            'create' => Pages\CreatePendidikanPegawai::route('/create'),
            'view' => Pages\ViewPendidikanPegawai::route('/{record}'),
            'edit' => Pages\EditPendidikanPegawai::route('/{record}/edit'),
        ];
    }
}
