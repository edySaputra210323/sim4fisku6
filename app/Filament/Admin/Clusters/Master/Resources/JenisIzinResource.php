<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\JenisIzin;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Clusters\Master;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\JenisIzinResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\JenisIzinResource\RelationManagers;

class JenisIzinResource extends Resource
{
    protected static ?string $model = JenisIzin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                ->schema([
                Forms\Components\TextInput::make('nama_jenis_izin')
                    ->label('Jenis Izin')
                    ->placeholder('Contoh: Terlambat Hadir')
                    ->required(),
                Forms\Components\TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Contoh: Terlambat Hadir')
                    ->required(),
                Forms\Components\Toggle::make('status')
                    ->required()
                    ->default(false),
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_jenis_izin')
                    ->label('Tahun Ajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('status')
                    ->label('Status Aktif')
                    ->alignCenter()
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageJenisIzins::route('/'),
        ];
    }
}
