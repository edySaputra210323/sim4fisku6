<?php

namespace App\Filament\Admin\Resources;

use App\Models\Atk;
use App\Models\KategoriAtk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section as FormSection;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AtkResource\Pages;

class AtkResource extends Resource
{
    protected static ?string $model = Atk::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'ATK';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Stock ATK';
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

                        Forms\Components\Select::make('kategori_atk_id')
                            ->label('Kategori ATK')
                            ->placeholder('Pilih Kategori ATK')
                            ->required()
                            ->searchable()
                            ->options(KategoriAtk::orderBy('nama_kategori')->get()->pluck('nama_kategori', 'id')),

                        Grid::make(['md' => 3])
                            ->schema([
                                Forms\Components\Select::make('satuan')
                                    ->label('Satuan')
                                    ->required()
                                    ->options([
                                        'pcs' => 'Pcs',
                                        'buah' => 'Buah',
                                        'pack' => 'Pack',
                                        'rim' => 'Rim',
                                        'box' => 'Box',
                                        'lusin' => 'Lusin',
                                        'set' => 'Set',
                                        'botol' => 'Botol',
                                        'tube' => 'Tube',
                                        'roll' => 'Roll',
                                        'lembar' => 'Lembar',
                                        'kg' => 'Kilogram',
                                        'liter' => 'Liter',
                                    ])
                                    ->searchable()
                                    ->placeholder('Pilih Satuan'),

                                Forms\Components\TextInput::make('stock_awal')
                                    ->label('Stok Awal')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord)
                                    ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),

                                Forms\Components\TextInput::make('stock')
                                    ->label('Stok Berjalan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\CreateRecord),
                            ]),

                        Forms\Components\Textarea::make('keterangan')
                            ->rows(3)
                            ->maxLength(255)
                            ->columnSpan('full'),
                    ])
                    ->columns(2)
                    ->columnSpan('2'),

                FormSection::make('Upload Foto ATK')
                    ->description('format: JPEG, JPG, atau PNG')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_atk')
                            ->disk('public')
                            ->label(false)
                            ->directory('atk')
                            ->image()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                            ->rules(['mimes:jpeg,jpg,png'])
                            ->validationMessages([
                                'mimes' => 'Format file harus JPEG, JPG, atau PNG.',
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpan('1'),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->recordAction(null)
            ->recordUrl(null)
            ->extremePaginationLinks()
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->poll('5s')
            ->recordClasses(fn () => 'table-vertical-align-top')
            ->columns([
                Split::make([
                    ImageColumn::make('foto_atk')
                        ->label('Foto')
                        ->disk('public')
                        ->height(50)
                        ->circular()
                        ->grow(false)
                        ->simpleLightbox(),

                    TextColumn::make('nama_atk')
                        ->weight(FontWeight::Bold)
                        ->searchable()
                        ->sortable(),

                    Stack::make([
                        TextColumn::make('kategoriAtk.nama_kategori')
                            ->label('Kategori')
                            ->icon('heroicon-m-rectangle-stack'),

                        TextColumn::make('stok_dengan_satuan')
                            ->label('Stok')
                            ->state(fn ($record) => "{$record->stock} {$record->satuan}")
                            ->badge()
                            ->color(fn ($record): string => match (true) {
                                $record->stock < 3 => 'danger',
                                $record->stock < 10 => 'warning',
                                default => 'success',
                            }),
                    ]),
                ]),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->color('primary')
                    ->icon('heroicon-m-eye'),
    
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->icon('heroicon-m-pencil-square'),
    
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Hapus ATK'),
            ])
            ->headerActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
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
            ->with(['kategoriAtk'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
