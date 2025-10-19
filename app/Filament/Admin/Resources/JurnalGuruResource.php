<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pegawai;
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

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Jurnal Guru';

    protected static ?string $pluralModelLabel = 'Jurnal Guru';

    protected static ?string $modelLabel = 'Jurnal Guru';

    protected static ?string $slug = 'jurnal-guru';

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
                        Forms\Components\Select::make('mapel_id')
                            ->label('Mata Pelajaran')
                            ->relationship('mapel', 'nama_mapel')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('pegawai_id')
                            ->label('Guru')
                            ->options(\App\Models\Pegawai::pluck('nm_pegawai', 'id'))
                            ->default(fn () => Auth::user()->pegawai_id ?? null)
                            ->visible(fn () => Auth::user()?->hasRole('superadmin')),

                       Forms\Components\Select::make('jam_ke_multiple')
                        ->label('Jam Ke')
                        ->multiple()
                        ->options([
                            1 => 'Jam ke-1',
                            2 => 'Jam ke-2',
                            3 => 'Jam ke-3',
                            4 => 'Jam ke-4',
                            5 => 'Jam ke-5',
                            6 => 'Jam ke-6',
                            7 => 'Jam ke-7',
                        ])
                        ->dehydrated(false) // jangan simpan ke kolom di tabel utama
                        ->afterStateHydrated(function ($set, $record) {
                            // Saat form edit, isi data berdasarkan relasi jam
                            if ($record) {
                                $set('jam_ke_multiple', $record->jam->pluck('jam_ke')->toArray());
                            }
                        })
                        ->afterStateUpdated(function ($state, $record) {
                            if (!$record) return;

                            // Sinkronkan dengan tabel pivot
                            $record->jam()->delete(); // hapus dulu semua jam lama
                            foreach ($state as $jamKe) {
                                $record->jam()->create(['jam_ke' => $jamKe]);
                            }
                        }),

                        Forms\Components\TextInput::make('materi')
                            ->label('Materi Pembelajaran')
                            ->maxLength(255)
                            ->required(),

                        Forms\Components\Textarea::make('kegiatan')
                            ->label('Kegiatan Pembelajaran')
                            ->rows(3)
                            ->maxLength(500)
                            ->required(),
                    ])->columns(2),

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
                        ])
                        ->columns(2)
                        ->createItemButtonLabel('Tambah Siswa')
                        ->live(onBlur: true) // biar re-render setelah inputan berubah
                    
                    
                                    ]),
                            ])->columns(2);
                            
                    }
        public static function table(Table $table): Table
        {
            return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d F Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('guru.nm_pegawai')
                    ->label('Guru')
                    ->sortable(),

                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->label('Mapel')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_ke')
                    ->label('Jam Ke')
                    ->formatStateUsing(function ($state) {
                    if (is_array($state)) {
                        $count = count($state);

                        if ($count === 0) {
                            return '-';
                        } elseif ($count === 1) {
                            return $state[0];
                        } elseif ($count === 2) {
                            return implode(' & ', $state);
                        } else {
                            // contoh: 1, 2, 3 & 4
                            $last = array_pop($state);
                            return implode(', ', $state) . ' & ' . $last;
                        }
                    }

                    return $state;
                })

                    ->alignCenter(),

                Tables\Columns\TextColumn::make('materi')
                    ->label('Materi')
                    ->limit(30)
                    ->wrap(),

                Tables\Columns\TextColumn::make('absensi')
                    ->label('Siswa Tidak Hadir')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->absensi->isEmpty()) {
                            return '<span class="text-green-600 font-medium">Semua hadir</span>';
                        }

                        $result = '<ul class="list-disc list-inside space-y-1">';
                        foreach ($record->absensi as $absen) {
                            $nama = $absen->riwayatKelas?->dataSiswa?->nama_siswa ?? 'Tidak diketahui';
                            $status = ucfirst($absen->status);

                            $color = match ($absen->status) {
                                'sakit' => 'bg-yellow-100 text-yellow-800',
                                'izin'  => 'bg-blue-100 text-blue-800',
                                'alpa'  => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };

                            $result .= "
                                <li>
                                    <span class='font-semibold'>{$nama}</span> 
                                    <span class='px-2 py-0.5 rounded text-xs {$color}'>{$status}</span>
                                </li>";
                        }
                        $result .= '</ul>';
                        return $result;
                    })
                    ->extraAttributes(['style' => 'max-height: 160px; overflow-y: auto;']),                
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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
