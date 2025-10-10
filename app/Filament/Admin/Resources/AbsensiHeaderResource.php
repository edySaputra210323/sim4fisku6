<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Pegawai;
use App\Models\Semester;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use App\Models\AbsensiDetail;
use App\Models\AbsensiHeader;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;
use App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers\AbsensiDetailRelationManager;
use App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers\AbsensiDetailsRelationManager;

class AbsensiHeaderResource extends Resource
{
    protected static ?string $model = AbsensiHeader::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Select::make('kelas_id')
                ->label('Kelas')
                ->relationship('kelas', 'nama_kelas')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(fn ($record) => $record !== null),

            Forms\Components\Select::make('mapel_id')
                ->label('Mapel')
                ->relationship('mapel', 'nama_mapel')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(fn ($record) => $record !== null),

                   // tampilkan hanya jika superadmin
                Forms\Components\Select::make('pegawai_id')
                ->label('Guru')
                ->relationship('guru', 'nm_pegawai')
                ->searchable()
                ->preload()
                ->required()
                ->hidden(fn () => Auth::user()->hasRole('guru'))
                ->disabled(fn ($record) => $record !== null),

            // Forms\Components\Select::make('tahun_ajaran_id')
            //     ->label('Tahun Ajaran')
            //     ->relationship('tahunAjaran', 'th_ajaran')
            //     ->searchable()
            //     ->preload()
            //     ->required()
            //     ->disabled(fn ($record) => $record !== null),

            // Forms\Components\Select::make('semester_id')
            //     ->label('Semester')
            //     ->relationship('semester', 'nm_semester')
            //     ->searchable()
            //     ->preload()
            //     ->required()
            //     ->disabled(fn ($record) => $record !== null),

            Forms\Components\DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->disabled(fn ($record) => $record !== null),

            Forms\Components\TimePicker::make('jam_mulai')
                ->label('Jam Mulai')
                ->disabled(fn ($record) => $record !== null),

            Forms\Components\TimePicker::make('jam_selesai')
                ->label('Jam Selesai')
                ->disabled(fn ($record) => $record !== null),

            Forms\Components\TextInput::make('pertemuan_ke')
                ->numeric()
                ->label('Pertemuan Ke'),

            Forms\Components\TextInput::make('kegiatan')
                ->label('Kegiatan')
                ->maxLength(255)
                ->disabled(fn ($record) => $record !== null),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Tables\Grouping\Group::make('tanggal')
                    ->label('Tanggal')
                    ->date(),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d F Y') // 📅 Format tanggal Indonesia: 28 Januari 2025
                    ->sortable()
                    ->icon('heroicon-o-calendar') // ikon kecil biar manis
                    ->weight(FontWeight::Medium)
                    ->alignCenter(),
    
                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable()
                    ->icon('heroicon-o-book-open')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->mapel->nama_mapel ?? '-'),
    
                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Guru')
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->wrap()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->guru->nm_pegawai ?? '-'),
    
                Tables\Columns\TextColumn::make('pertemuan_ke')
                    ->label('Pertemuan')
                    ->alignCenter()
                    ->badge()
                    ->color('gray'),
    
                Tables\Columns\TextColumn::make('kegiatan')
                    ->label('Kegiatan')
                    ->wrap()
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->kegiatan ?? '-'),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
    
                Tables\Columns\TextColumn::make('total_siswa')
                    ->label('Total')
                    ->weight(FontWeight::Bold)
                    ->alignCenter(),
    
                Tables\Columns\TextColumn::make('hadir_count')
                    ->label('H')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
    
                Tables\Columns\TextColumn::make('sakit_count')
                    ->label('S')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),
    
                Tables\Columns\TextColumn::make('izin_count')
                    ->label('I')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
    
                Tables\Columns\TextColumn::make('alpa_count')
                    ->label('A')
                    ->badge()
                    ->color('danger')
                    ->alignCenter(),
            ])
            ->defaultSort('tanggal', 'desc')
            ->modifyQueryUsing(fn ($query) =>
                $query->withCount([
                    'absensiDetails as total_siswa',
                    'absensiDetails as hadir_count' => fn ($q) => $q->where('status', 'hadir'),
                    'absensiDetails as sakit_count' => fn ($q) => $q->where('status', 'sakit'),
                    'absensiDetails as izin_count'  => fn ($q) => $q->where('status', 'izin'),
                    'absensiDetails as alpa_count'  => fn ($q) => $q->where('status', 'alpa'),
                ])
            )
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->label('Filter Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->tooltip('Lihat detail absensi'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->tooltip('Ubah data absensi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation(),
                ]),
            ])
            ->striped() // Garis bergantian untuk baris biar rapi
            ->paginated([10, 25, 50]) // Tambahkan opsi jumlah baris per halaman
            ->emptyStateHeading('Belum ada data absensi')
            ->emptyStateDescription('Silakan tambahkan data absensi melalui tombol “+ Tambah Absensi”.');
    }
    


    public static function getRelations(): array
    {
        return [
            AbsensiDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiHeaders::route('/'),
            'create' => Pages\CreateAbsensiHeader::route('/create'),
            'edit' => Pages\EditAbsensiHeader::route('/{record}/edit'),
            'view' => Pages\ViewAbsensiHeader::route('/{record}'),
        ];
    }

        /**
     * Setelah header dibuat, generate detail siswa otomatis
     */
    public static function afterCreate($record): void
    {
        $siswaKelas = RiwayatKelas::where('kelas_id', $record->kelas_id)
            ->where('tahun_ajaran_id', $record->tahun_ajaran_id)
            ->where('semester_id', $record->semester_id)
            ->get();

        foreach ($siswaKelas as $riwayat) {
            AbsensiDetail::firstOrCreate([
                'absensi_header_id' => $record->id,
                'riwayat_kelas_id'  => $riwayat->id,
            ], [
                'status' => 'hadir',
            ]);
        }
    }
}
