<?php

namespace App\Filament\Admin\Resources;

use App\Models\Atk;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KategoriAtk;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Resources\AtkResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section as FormSection;
use App\Filament\Admin\Resources\AtkResource\RelationManagers;

class AtkResource extends Resource
{
    protected static ?string $model = Atk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'ATK';

    protected static ?string $modelLabel = 'ATK';

    protected static ?string $pluralModelLabel = 'ATK';

    protected static ?string $slug = 'atk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormSection::make()
                    ->schema([
                    Forms\Components\TextInput::make('code')
                        ->maxLength(255)
                        ->unique()
                        ->hidden(),
                    Forms\Components\TextInput::make('nama_atk')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('categori_atk_id')
                        ->label('Kategori ATK')
                        ->placeholder('Pilih Kategori ATK')
                        ->required()
                        ->searchable()
                        ->options(KategoriAtk::orderBy('nama_kategori')->get()->pluck('nama_kategori', 'id')),
                    Forms\Components\TextInput::make('satuan')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('keterangan')
                        ->rows(3)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('stock')
                        ->numeric()
                        ->default(0)
                        ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),                    
                    ])->columns(1)->columnSpan('1'),
                    FormSection::make('Upload Foto ATK')
                    ->description('format: JPEG, JPG, atau PNG')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_atk')
                                ->disk('public')
                                ->label(false)
                                ->directory('atk')
                                ->image(['jpeg', 'jpg', 'png'])
                                ->maxSize(2048)
                                ->visibility('public')
                    ])->columns(1)->columnSpan('1'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_atk')
                    ->label('Foto ATK')
                    ->disk('public')
                    ->height(40)
                    ->grow(false)
                    ->simpleLightbox(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nama_atk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori_atk.nama_kategori')
                    ->label('Kategori ATK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('satuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListAtks::route('/'),
            'create' => Pages\CreateAtk::route('/create'),
            'view' => Pages\ViewAtk::route('/{record}'),
            'edit' => Pages\EditAtk::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
