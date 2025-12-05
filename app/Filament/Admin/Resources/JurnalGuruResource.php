<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pegawai;
use App\Enums\JamKeEnum;
use App\Models\Semester;
use Filament\Forms\Form;
use App\Models\JurnalGuru;
use Filament\Tables\Table;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\JurnalGuruResource\Pages;
use App\Filament\Admin\Resources\JurnalGuruResource\RelationManagers;

class JurnalGuruResource extends Resource
{
    protected static ?string $model = JurnalGuru::class;

    protected static ?string $navigationGroup = 'Data Akademik';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Jurnal Mengajar';

    protected static ?string $pluralModelLabel = 'Jurnal Mengajar';

    protected static ?string $modelLabel = 'Jurnal Mengajar';

    protected static ?string $slug = 'jurnal-mengajar';

    // ---------------------------
    // 📋 FORM
    // ---------------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Jurnal Guru')
                    ->schema([
                        Grid::make([
                                'sm' => 2,
                        ])
                            ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('kelas_id')
                            ->label('Kelas')
                            ->relationship('kelas', 'nama_kelas')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set) => $set('absensi', [])),
                            ]),
                        Grid::make([
                                'sm' => 2,
                        ])
                            ->schema([
                        Forms\Components\Select::make('mapel_id')
                            ->label('Mata Pelajaran')
                            ->relationship('mapel', 'nama_mapel')
                            ->searchable()
                            ->preload()
                            ->required(),
                            Forms\Components\Select::make('jam_ke_multiple')
                            ->label('Jam Ke')
                            ->multiple()
                            ->options(JamKeEnum::options())
                            ->required()
                            ->dehydrated(false) // jangan simpan ke kolom utama
                            ->afterStateHydrated(function ($set, $record) {
                                if ($record) {
                                    $set('jam_ke_multiple', $record->jam->pluck('jam_ke')->toArray());
                                }
                            }),
                        ]),
                        Forms\Components\Select::make('pegawai_id')
                            ->label('Guru')
                            ->options(\App\Models\Pegawai::pluck('nm_pegawai', 'id'))
                            ->default(fn () => Auth::user()->pegawai_id ?? null)
                            ->visible(fn () => Auth::user()?->hasRole('superadmin'))
                            ->required()
                            ->searchable()
                            ->preload(),

                       
                        Forms\Components\TextInput::make('materi')
                            ->label('Materi')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\Textarea::make('kegiatan')
                            ->label('Kegiatan Pembelajaran')
                            ->rows(3)
                            ->maxLength(500)
                            ->required(),
                    ])->columnSpan(1)->columns(1),

                Section::make('Siswa Tidak Hadir')
                    ->description('Isi data siswa yang tidak hadir di jam ini.')
                    ->schema([
                        Forms\Components\Repeater::make('absensi')
                        ->relationship() // pivot ke tabel absensi
                        ->label('Daftar Ketidakhadiran')
                        ->reactive()
                        ->schema([
                    Forms\Components\Select::make('riwayat_kelas_id')
                        ->label('Nama Siswa')
                        ->searchable()
                        ->reactive()
                        ->options(function (callable $get) {
                            $kelasId = $get('../../kelas_id');
                            $tahunAktif = \App\Models\TahunAjaran::where('status', 1)->first();
                            $semesterAktif = \App\Models\Semester::where('status', 1)->first();

                            if (!$kelasId || !$tahunAktif || !$semesterAktif) {
                                return [];
                            }
                            $selected = collect($get('../../absensi'))
                                ->pluck('riwayat_kelas_id')
                                ->filter()
                                ->toArray();
                        return \App\Models\RiwayatKelas::where('kelas_id', $kelasId)
                            ->where('tahun_ajaran_id', $tahunAktif->id)
                            ->where('semester_id', $semesterAktif->id)
                            ->where('status_aktif', 1)
                            ->whereNotIn('id', $selected)
                            ->with('dataSiswa')
                            ->get()
                            ->pluck('dataSiswa.nama_siswa', 'id')
                            ->toArray();
                                })
                                ->getOptionLabelUsing(function ($value) {
                                    $riwayat = \App\Models\RiwayatKelas::with('dataSiswa')->find($value);
                                    return $riwayat?->dataSiswa?->nama_siswa ?? 'Tidak diketahui';
                                })
                                ->required(),
                     Forms\Components\Select::make('status')
                            ->label('Keterangan')
                            ->options([
                                'sakit' => 'Sakit',
                                'izin'  => 'Izin',
                                'alpa'  => 'Alpa',
                                ])
                                ->required(),
                            ])->columns(2)
                            
                            ->createItemButtonLabel('Tambah Siswa')
                            ->live(onBlur: true) // biar re-render setelah inputan berubah
                    
                                ])->columnSpan(2),
                            ])->columns(3);
                            
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
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Guru')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->label('Mapel')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam')
                ->label('Jam Ke')
                ->getStateUsing(function ($record) {
                    // Pastikan relasi ter-load. Jika belum, ambil dari DB.
                    if (! $record->relationLoaded('jam')) {
                        $record->load('jam');
                    }

                    $jamCollection = $record->jam ?? collect();

                    // Ambil jam_ke yang unik, sort, dan kembalikan sebagai array (integer)
                    $arr = collect($jamCollection)
                        ->pluck('jam_ke')
                        ->filter()             // hilangkan null/empty
                        ->unique()
                        ->map(fn($v) => (int) $v)
                        ->sort()
                        ->values()
                        ->all();

                    return $arr; // pasti array
                })
                ->formatStateUsing(function ($state) {
                    // Normalisasi: jika bukan array, coba decode / parse jadi array
                    if (is_string($state)) {
                        // coba decode JSON
                        $decoded = json_decode($state, true);
                        if (is_array($decoded)) {
                            $state = $decoded;
                        } else {
                            // fallback: pecah dengan koma (untuk kasus "1,2" atau "1, 2")
                            $parts = preg_split('/\s*,\s*/', $state, -1, PREG_SPLIT_NO_EMPTY);
                            $state = array_map(fn($v) => trim($v), $parts);
                        }
                    }

                    // Jika masih bukan array, buat jadi array 1 elemen
                    if (! is_array($state)) {
                        $state = [$state];
                    }

                    // Pastikan elemen numeric dan terurut
                    $state = array_values(array_filter(array_map(fn($v) => $v === '' ? null : (int) $v, $state), fn($v) => $v !== null));
                    sort($state, SORT_NUMERIC);

                    if (empty($state)) {
                        return '-';
                    }

                    if (count($state) === 1) {
                        return (string) $state[0];
                    }

                    // Ambil semua kecuali terakhir
                    $allExceptLast = array_slice($state, 0, -1);
                    $last = end($state);

                    // Gabungkan: "1, 2 & 3" atau untuk dua elemen menjadi "1 & 2"
                    return implode(', ', $allExceptLast) . ' & ' . $last;
                })
                ->alignCenter(),

                Tables\Columns\TextColumn::make('materi')
                    ->label('Materi')
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('absensi_html')
                ->label('Siswa Tidak Hadir')
                ->html()
                ->extraAttributes(['style' => 'max-height: 160px; overflow-y: auto;'])
                ->alignLeft(),
                            
                        ])
            ->defaultSort('tanggal', 'desc')
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
                Tables\Actions\EditAction::make()
                ->iconButton()
                ->color('warning')
                ->tooltip('Ubah Jurnal')
                ->icon('heroicon-o-pencil-square'),
            Tables\Actions\DeleteAction::make()
                ->iconButton()
                ->color('danger')
                ->tooltip('Hapus Jurnal')
                ->icon('heroicon-o-trash')
                ->modalHeading('Hapus Jurnal'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListJurnalGurus::route('/'),
            'create' => Pages\CreateJurnalGuru::route('/create'),
            'edit' => Pages\EditJurnalGuru::route('/{record}/edit'),
        ];
    }
}
