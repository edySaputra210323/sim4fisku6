<?php

namespace App\Filament\Admin\Resources;

use App\Models\AtkMasuk;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AtkMasukResource\Pages;

class AtkMasukResource extends Resource
{
    protected static ?string $model = AtkMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'ATK';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'ATK Masuk';
    protected static ?string $modelLabel = 'ATK Masuk';
    protected static ?string $pluralModelLabel = 'ATK Masuk';
    protected static ?string $slug = 'atk-masuk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Barang Masuk')
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal')
                ->default(now())
                ->required(),

            Forms\Components\TextInput::make('nomor_nota')
                ->label('Nomor Nota Supplier')
                ->placeholder('Opsional, isi jika ada nomor nota dari toko. Jika kosong akan dibuat otomatis.')
                ->maxLength(255),

            Forms\Components\FileUpload::make('file_nota')
                ->label('Upload Nota (PDF/JPG/PNG)')
                ->disk('public')
                ->directory('nota')
                ->acceptedFileTypes(['application/pdf','image/jpeg','image/png'])
                ->maxSize(2048)
                ->nullable(),
                ]),
            

            Forms\Components\Section::make('Detail Barang Masuk')
                ->schema([
                    Forms\Components\Repeater::make('details')
                        ->relationship() // otomatis ke detail_atk_masuk
                        ->schema([
                            Forms\Components\Select::make('atk_id')
                                ->label('Barang ATK')
                                ->relationship('atk', 'nama_atk')
                                ->searchable()
                                ->required(),

                            Forms\Components\TextInput::make('qty')
                                ->numeric()
                                ->minValue(1)
                                ->required(),

                            Forms\Components\TextInput::make('harga_satuan')
                                ->numeric()
                                ->minValue(0)
                                ->prefix('Rp')
                                ->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->createItemButtonLabel('Tambah Barang'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_nota')
                    ->label('Nomor Nota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('file_nota')
                    ->label('Nota')
                    ->disk('public')
                    ->height(50)
                    ->grow(false)
                    ->defaultImageUrl(url('images/placeholder.jpg'))
                    ->simpleLightbox()
                    ->getStateUsing(function ($record) {
                        $path = $record->file_nota;
                        if ($path && Storage::disk('public')->exists($path)) {
                            return Storage::disk('public')->url($path);
                        }
                        return null;
                    }),

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
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->icon('heroicon-m-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Hapus ATK'),

                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->icon('heroicon-m-eye'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('nomor_nota')
                    ->label('Nomor Nota')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('nomor_nota');
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
            'index' => Pages\ListAtkMasuks::route('/'),
            'create' => Pages\CreateAtkMasuk::route('/create'),
            'view' => Pages\ViewAtkMasuk::route('/{record}'),
            'edit' => Pages\EditAtkMasuk::route('/{record}/edit'),
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
