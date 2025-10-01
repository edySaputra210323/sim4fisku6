<?php

namespace App\Filament\Admin\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Semester;
use Filament\Forms\Form;
use App\Models\AtkKeluar;
use Filament\Tables\Table;
use App\Models\TahunAjaran;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AtkKeluarResource\Pages;
use App\Filament\Admin\Resources\AtkKeluarResource\RelationManagers;

class AtkKeluarResource extends Resource
{
    protected static ?string $model = AtkKeluar::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'ATK';
    protected static ?string $navigationLabel = 'Pengambilan ATK';
    protected static ?int $navigationSort = 3;
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
                ->preload()
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
                        ->options(
                            \App\Models\Atk::where('stock', '>', 0)
                                ->orderBy('nama_atk')
                                ->get()
                                ->mapWithKeys(fn ($atk) => [
                                    $atk->id => "{$atk->nama_atk} (Stok: {$atk->stock}" . ($atk->satuan ? " {$atk->satuan}" : "") . ")"
                                ])
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive(), // supaya qty bisa tahu stok saat atk diganti

                    Forms\Components\TextInput::make('qty')
                        ->label('Jumlah')
                        ->numeric()
                        ->minValue(1)
                        ->required()
                        ->reactive()
                        ->rule(
                            fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                $status = $get('../../status'); // ambil status parent
                                $stok   = \App\Models\Atk::find($get('atk_id'))?->stock ?? 0;
                        
                                // ✅ Validasi hanya kalau status = draft
                                if ($status === 'draft' && $value > $stok) {
                                    $fail("Jumlah melebihi stok yang tersedia ($stok).");
                                }
                            }
                        ),
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
        $activeTahunAjaran = cache()->remember(
            'active_th_ajaran',
            now()->addMinutes(1),
            fn () => TahunAjaran::where('status', true)->first()
        );
    
        $activeSemester = cache()->remember(
            'active_semester',
            now()->addMinutes(1),
            fn () => Semester::where('status', true)->first()
        );
    
        return $table
        ->modifyQueryUsing(function (Builder $query) use ($activeTahunAjaran, $activeSemester) {
            if (! auth()->user()->hasRole('superadmin')) {
                $query->where('ditambah_oleh_id', auth()->id());
            }
        
            // ambil filter dari request
            $filters = request()->input('tableFilters', []);
        
            $filterTahun = $filters['tahun_ajaran_id']['value'] ?? null;
            $filterSemester = $filters['semester_id']['value'] ?? null;
        
            if ($filterTahun || $filterSemester) {
                // ✅ kalau user pilih filter manual → pakai filter itu
                return $query->when($filterTahun, fn($q) => $q->where('tahun_ajaran_id', $filterTahun))
                             ->when($filterSemester, fn($q) => $q->where('semester_id', $filterSemester))
                             ->orderByDesc('id');
            }
        
            if ($activeTahunAjaran && $activeSemester) {
                // ✅ default → periode aktif
                return $query->where('tahun_ajaran_id', $activeTahunAjaran->id)
                             ->where('semester_id', $activeSemester->id)
                             ->orderByDesc('id');
            }
        
            // ❌ tidak ada periode aktif → kosong
            return $query->whereRaw('0 = 1');
        })
        
            ->recordAction(null)
            ->recordUrl(null)
            ->extremePaginationLinks()
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->poll('5s')
            ->recordClasses(fn () => 'table-vertical-align-top')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Transaksi')
                    ->sortable()
                    ->formatStateUsing(fn ($state) =>
                        \Carbon\Carbon::parse($state)->translatedFormat('d F Y H:i')
                    ),
                Tables\Columns\TextColumn::make('pegawai.nm_pegawai')
                    ->label('Pegawai / Guru')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'secondary' => 'draft',     // abu-abu untuk draft
                        'success'   => 'verified',  // hijau untuk terverifikasi
                        'danger'    => 'canceled',  // merah untuk dibatalkan
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft'    => 'Draft',
                        'verified' => 'Terverifikasi',
                        'canceled' => 'Dibatalkan',
                        default    => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Ditambah Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verifiedBy.email')->label('Verifikator')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verified_at')
                ->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('canceledBy.email')->label('Pembatal')
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('canceled_at')->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'th_ajaran')
                    ->default($activeTahunAjaran?->id)
                    ->preload(),
            
                    Tables\Filters\SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->options(
                        \App\Models\Semester::with('tahunAjaran')
                            ->get()
                            ->mapWithKeys(fn ($semester) => [
                                $semester->id => $semester->nm_semester . ' - ' . ($semester->tahunAjaran?->th_ajaran ?? '-'),
                            ])
                    )
                    ->searchable()
                    ->preload()
                    ->default($activeSemester?->id),
            
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
                    ->modalHeading('Hapus Transaksi ATK'),
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
