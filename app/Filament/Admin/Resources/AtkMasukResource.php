<?php

namespace App\Filament\Admin\Resources;

use App\Models\Atk;
use App\Models\AtkMasuk;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Awcodes\TableRepeater\Components\TableRepeater;
use Awcodes\TableRepeater\Header;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AtkMasukResource\Pages;

class AtkMasukResource extends Resource
{
    protected static ?string $model = AtkMasuk::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'ATK';
    protected static ?string $navigationLabel = 'Daftar ATK Masuk';
    protected static ?string $modelLabel = 'ATK Masuk';
    protected static ?string $pluralModelLabel = 'ATK Masuk';
    protected static ?string $slug = 'atk-masuk';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Nota')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('nota')
                            ->label('Nomor Nota')
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now())
                            ->disabled(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    ]),
                    Forms\Components\FileUpload::make('foto_nota')
                        ->label('Foto Nota')
                        ->image()
                        ->disk('public')
                        ->directory('notas')
                        ->maxSize(2048)
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png'])
                        ->rules(['mimes:jpeg,jpg,png'])
                        ->validationMessages([
                            'mimes' => 'Format file harus JPEG, JPG, atau PNG.',
                        ])
                ]),
            Forms\Components\Section::make('Detail Barang')
                ->schema([
                    TableRepeater::make('items')
                        ->label('Daftar Barang')
                        ->headers([
                            Header::make('nama_atk')->label('Nama ATK')->width('30%')->align(Alignment::Left),
                            Header::make('qty')->label('Jumlah')->width('20%')->align(Alignment::Center),
                            Header::make('harga_satuan')->label('Harga Satuan (Rp)')->width('25%')->align(Alignment::Right),
                            Header::make('total_harga')->label('Total Harga (Rp)')->width('25%')->align(Alignment::Right),
                        ])
                        ->schema([
                            Forms\Components\Select::make('atk_id')
                                ->label('Nama ATK')
                                ->options(Atk::orderBy('nama_atk')->pluck('nama_atk', 'id'))
                                ->required()
                                ->searchable()
                                ->placeholder('Pilih ATK'),
                            Forms\Components\TextInput::make('qty')
                                ->label('Jumlah')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->rules(['integer', 'min:1'])
                                ->validationMessages([
                                    'min' => 'Jumlah harus lebih dari 0.',
                                    'integer' => 'Jumlah harus berupa angka bulat.',
                                ])
                                ->placeholder('0')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $harga = floatval($get('harga_satuan') ?? 0);
                                    $total = floatval($state) * $harga;
                                    $set('total_harga', $total);
                                }),
                            Forms\Components\TextInput::make('harga_satuan')
                                ->label('Harga Satuan (Rp)')
                                ->numeric()
                                ->required()
                                ->minValue(0)
                                ->rules(['numeric', 'min:0'])
                                ->validationMessages([
                                    'min' => 'Harga satuan tidak boleh negatif.',
                                ])
                                ->placeholder('0')
                                ->prefix('Rp')
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                    $qty = floatval($get('qty') ?? 0);
                                    $total = $qty * floatval($state);
                                    $set('total_harga', $total);
                                }),
                            Forms\Components\TextInput::make('total_harga')
                                ->label('Total Harga (Rp)')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(false)
                                ->prefix('Rp')
                                ->placeholder('0'),
                        ])
                        ->default([])
                        ->addActionLabel('Tambah Barang')
                        ->required()
                        ->minItems(1)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nota')
                    ->label('Nomor Nota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('atk.nama_atk')
                    ->label('Nama ATK')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => "$state unit"),
                Tables\Columns\TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('foto_nota')
                    ->label('Foto Nota')
                    ->disk('public')
                    ->height(50)
                    ->grow(false)
                    ->defaultImageUrl(url('images/placeholder.jpg'))
                    ->simpleLightbox()
                    ->getStateUsing(function ($record) {
                        $path = $record->foto_nota;
                        if ($path && Storage::disk('public')->exists($path)) {
                            return Storage::disk('public')->url($path);
                        }
                        \Log::warning('Image not found for path: ' . ($path ?? 'No path'));
                        return null;
                    }),
                // Tables\Columns\TextColumn::make('tahun_ajaran.tahun')
                //     ->label('Tahun Ajaran')
                //     ->searchable()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('semester.nama')
                //     ->label('Semester')
                //     ->searchable()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('ditambahOleh.name')
                //     ->label('Ditambah Oleh')
                //     ->searchable(),
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
                // Tables\Filters\TrashedFilter::make(),
                // Tables\Filters\SelectFilter::make('atk_id')
                //     ->label('Nama ATK')
                //     ->relationship('atk', 'nama_atk'),
                // Tables\Filters\SelectFilter::make('tahun_ajaran_id')
                //     ->label('Tahun Ajaran')
                //     ->relationship('tahun_ajaran', 'tahun'),
                // Tables\Filters\SelectFilter::make('semester_id')
                //     ->label('Semester')
                //     ->relationship('semester', 'nama'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('nota')
                    ->label('Nomor Nota')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('nota');
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