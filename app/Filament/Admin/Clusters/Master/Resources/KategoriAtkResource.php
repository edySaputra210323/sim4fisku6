<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KategoriAtk;
use Filament\Resources\Resource;
use App\Filament\Admin\Clusters\Master;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\KategoriAtkResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\KategoriAtkResource\RelationManagers;

class KategoriAtkResource extends Resource
{
    protected static ?string $model = KategoriAtk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kategori ATK';

    protected static ?string $modelLabel = 'Kategori ATK';

    protected static ?string $pluralModelLabel = 'Kategori ATK';

    protected static ?string $slug = 'kategori-atk';

    protected static ?string $cluster = Master::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kategori')
                    ->required()
                    ->label('Nama Kategori ATK')
                    ->columnSpan('full')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No.')->state(
                    static function (HasTable $livewire, stdClass $rowLoop): string {
                        return (string) (
                            $rowLoop->iteration +
                            ($livewire->getTableRecordsPerPage() * (
                                $livewire->getTablePage() - 1
                            ))
                        );
                    }
                )
                ->label('No')
                ->width('50px') // Atur lebar kolom menjadi lebih kecil
                ->extraAttributes(['class' => 'text-sm']) // Atur ukuran font lebih kecil
                ->alignCenter(), // Opsional: Ratakan ke tengah untuk estetika,

                Tables\Columns\TextColumn::make('nama_kategori')
                    ->label('Nama Kategori ATK')
                    ->searchable(),
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
            'index' => Pages\ManageKategoriAtks::route('/'),
        ];
    }
}
