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
use Tables\Filters\Filter;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use App\Models\AbsensiDetail;
use App\Models\AbsensiHeader;
use Filament\Resources\Resource;
use Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
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

     protected static ?string $navigationGroup = 'Data Akademik';

    protected static ?string $navigationLabel = 'Jurnal Kelas';

    protected static ?string $pluralLabel = 'Jurnal Kelas';

    protected static ?string $modelLabel = 'Jurnal Kelas';

    protected static ?string $pluralModelLabel = 'Jurnal Kelas';

    protected static ?string $slug = 'jurnal-kelas';

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Data Absensi')
                ->description('Isi informasi dasar untuk absensi hari ini.')
                ->schema([
                    Forms\Components\Select::make('kelas_id')
                        ->label('Kelas')
                        ->relationship('kelas', 'nama_kelas')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $tahunAktif = \App\Models\TahunAjaran::where('status', 1)->first();
                            $semesterAktif = \App\Models\Semester::where('status', 1)->first();

                            $walas = \App\Models\RiwayatKelas::where('kelas_id', $state)
                                ->where('tahun_ajaran_id', $tahunAktif?->id)
                                ->where('semester_id', $semesterAktif?->id)
                                ->first()?->guru;

                            $set('pegawai_id', $walas?->id);
                        })
                        ->disabled(fn($record) => $record !== null),

                    Forms\Components\Select::make('pegawai_id')
                        ->label('Wali Kelas')
                        ->options(\App\Models\Pegawai::pluck('nm_pegawai', 'id'))
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Absensi')
                        ->default(now())
                        ->required()
                        ->disabled(fn($record) => $record !== null),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan / Keterangan Tambahan')
                        ->rows(3)
                        ->maxLength(255)
                        ->placeholder('Contoh: Beberapa siswa mengikuti lomba pramuka...')
                        ->columnSpanFull()
                        ->nullable(),
                ]),

            // ⚠️ Informasi tambahan (hanya muncul saat edit)
            Forms\Components\Section::make('Informasi')
                ->visible(fn($record) => $record !== null)
                ->schema([
                    Forms\Components\Placeholder::make('info_edit')
                        ->label('Perhatian')
                        ->content('Data dasar absensi (kelas, wali kelas, tanggal) tidak dapat diubah setelah absensi dibuat.')
                        ->helperText('Anda masih dapat memperbarui kehadiran siswa di bagian bawah.'),
                ]),
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
                    ->date('d F Y')
                    ->icon('heroicon-o-calendar')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Wali Kelas')
                    ->icon('heroicon-o-user')
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
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
          ->modifyQueryUsing(function (Builder $query) {
    return $query
        ->withCount([
            'absensiDetails as total_siswa',
            'absensiDetails as hadir_count' => fn($q) => $q->where('status', 'hadir'),
            'absensiDetails as sakit_count' => fn($q) => $q->where('status', 'sakit'),
            'absensiDetails as izin_count'  => fn($q) => $q->where('status', 'izin'),
            'absensiDetails as alpa_count'  => fn($q) => $q->where('status', 'alpa'),
        ]);
        })
        ->filters([
            Tables\Filters\Filter::make('tanggal')
                ->label('Filter Tanggal')
                ->form([
                    Forms\Components\DatePicker::make('from')->label('Dari'),
                    Forms\Components\DatePicker::make('until')->label('Sampai'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    // kalau user isi filter manual, pakai itu
                    if ($data['from'] || $data['until']) {
                        return $query
                            ->when($data['from'], fn($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['until'], fn($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }

                    // kalau tidak ada filter → tampilkan hanya hari ini
                    return $query->whereDate('tanggal', now()->toDateString());
                })
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
