<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StatusPegawai;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use App\Filament\Admin\Clusters\Master;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\StatusPegawaiResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\StatusPegawaiResource\RelationManagers;

class StatusPegawaiResource extends Resource
{
    protected static ?string $model = StatusPegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Status Pegawai';

    protected static ?string $modelLabel = 'Status Pegawai';

    protected static ?string $pluralModelLabel = 'Status Pegawai';

    protected static ?string $slug = 'status-pegawai';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama_status')
                    ->required()
                    ->placeholder('Masukkan nama status'),
                TextInput::make('kode')
                    ->placeholder('Masukkan kode status'),
                ColorPicker::make('warna')
                    ->placeholder('Masukkan warna status'),
                Textarea::make('keterangan')
                    ->placeholder('Masukkan keterangan status'),
                Toggle::make('is_active')
                    ->label('Aktif?')
                    ->default(true),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_status')
                    ->searchable(),
                TextColumn::make('kode')
                    ->searchable(),
                ColorColumn::make('warna'),
                TextColumn::make('keterangan')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean(),
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
            'index' => Pages\ManageStatusPegawais::route('/'),
        ];
    }
}
