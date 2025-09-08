<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\RelationManagers;

use App\Models\Atk;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details'; // pastikan sesuai nama relasi di model AtkKeluar

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('atk_id')
                    ->label('Barang ATK')
                    ->options(Atk::orderBy('nama_atk')->pluck('nama_atk', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('atk.nama_atk') // tampilkan nama barang
            ->columns([
                Tables\Columns\TextColumn::make('atk.nama_atk')
                    ->label('Barang ATK')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
