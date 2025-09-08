<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AtkKeluarResource\Pages;
use App\Filament\Admin\Resources\AtkKeluarResource\RelationManagers;
use App\Models\AtkKeluar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AtkKeluarResource extends Resource
{
    protected static ?string $model = AtkKeluar::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'ATK';
    protected static ?string $navigationLabel = 'Pengambilan Atk';
    protected static ?string $modelLabel = 'ATK Keluar';
    protected static ?string $pluralModelLabel = 'ATK Keluar';
    protected static ?string $slug = 'atk-keluar';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Informasi Transaksi')
        ->schema([
            Forms\Components\DateTimePicker::make('tanggal')
                ->label('Tanggal & Jam Transaksi')
                ->default(now())
                ->required()
                ->seconds(false),

            Forms\Components\Select::make('pegawai_id')
                ->label('Pegawai / Guru Penerima')
                ->relationship('pegawai', 'nm_pegawai')
                ->searchable()
                ->visible(fn () => auth()->user()->hasRole('superadmin')),

                Forms\Components\Select::make('status')
                ->label('Status Transaksi')
                ->options([
                    'draft' => 'Draft',
                    'verified' => 'Terverifikasi',
                    'canceled' => 'Dibatalkan',
                ])
                ->default('draft')
                ->reactive() // supaya perubahan langsung trigger $get()
                ->hidden(fn () => !auth()->user()->hasRole('superadmin')) // non-admin tidak bisa lihat
                ->columnSpan('full'),

                Forms\Components\Textarea::make('alasan_batal')
                ->label('Alasan Pembatalan')
                ->visible(fn ($get) => $get('status') === 'canceled')
                ->columnSpan('full'),
    ])
    ->columns(2),

            Forms\Components\Section::make('Detail Barang')
                ->schema([
                    Forms\Components\Repeater::make('details')
                ->relationship('details')
                ->schema([
                    Forms\Components\Select::make('atk_id')
                        ->label('Barang ATK')
                        ->options(\App\Models\Atk::orderBy('nama_atk')->pluck('nama_atk', 'id'))
                        ->searchable()
                        ->required()
                        ->reactive(), // supaya qty bisa tahu stok saat atk diganti

                    Forms\Components\TextInput::make('qty')
                        ->label('Jumlah')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->reactive()
                        ->rule(function ($get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $atkId = $get('atk_id');
                                if ($atkId) {
                                    $stok = \App\Models\Atk::find($atkId)?->stock ?? 0;
                                    if ($value > $stok) {
                                        $fail("Jumlah melebihi stok yang tersedia ($stok).");
                                    }
                                }
                            };
                        }),
                ])
                ->columns(2)
                ->defaultItems(1)
                ->createItemButtonLabel('Tambah Barang'),
                ])
                ->collapsible(),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            return $query->orderBy('id', 'desc');
        })
            ->recordAction(null)
            ->recordUrl(null)
            ->extremePaginationLinks()
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->poll('5s')
            ->recordClasses(function () {
                $classes = 'table-vertical-align-top ';
                return $classes;
            })
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Transaksi')
                    ->sortable()
                    ->formatStateUsing(fn ($state) =>
                        \Carbon\Carbon::parse($state)->translatedFormat('d F Y H:i')
                    ),
                Tables\Columns\TextColumn::make('pegawai.nm_pegawai')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahunAjaran.th_ajaran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.nm_semester')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('verified_by_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('canceled_by_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('canceled_at')
                    ->dateTime()
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
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->color('warning')
                    ->icon('heroicon-m-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->iconButton()
                    ->color('danger')
                    ->icon('heroicon-m-trash')
                    ->modalHeading('Hapus Transaksi Atk'),
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
            'index' => Pages\ListAtkKeluars::route('/'),
            'create' => Pages\CreateAtkKeluar::route('/create'),
            'view' => Pages\ViewAtkKeluar::route('/{record}'),
            'edit' => Pages\EditAtkKeluar::route('/{record}/edit'),
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
