<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\JadwalMengajar;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use App\Filament\Admin\Resources\JadwalMengajarResource\Pages;

class JadwalMengajarResource extends Resource
{
    protected static ?string $model = JadwalMengajar::class;

    protected static ?string $navigationGroup = 'Data Akademik';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Mengajar';

    protected static ?string $pluralLabel = 'Jadwal Mengajar';

    protected static ?string $modelLabel = 'Jadwal Mengajar';

    protected static ?string $pluralModelLabel = 'Jadwal Mengajar';

    protected static ?string $slug = 'jadwal-mengajar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('hari')
                    ->label('Hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
                    ->required(),

                Forms\Components\Select::make('jam_ke')
                    ->label('Jam Ke-')
                    ->options([
                        1 => 'Jam Ke-1',
                        2 => 'Jam Ke-2',
                        3 => 'Jam Ke-3',
                        4 => 'Jam Ke-4',
                        5 => 'Jam Ke-5',
                        6 => 'Jam Ke-6',
                        7 => 'Jam Ke-7',
                        8 => 'Jam Ke-8',
                        9 => 'Jam Ke-9',
                        10 => 'Jam Ke-10',
                    ])
                    ->multiple(),

                Forms\Components\TimePicker::make('jam_mulai')
                    ->label('Jam Mulai'),

                Forms\Components\TimePicker::make('jam_selesai')
                    ->label('Jam Selesai'),

                Forms\Components\Select::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('mapel_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mapel', 'nama_mapel')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('pegawai_id')
                    ->label('Guru Pengajar')
                    ->relationship('guru', 'nm_pegawai')
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->disabled()
                    ->relationship('tahunAjaran', 'th_ajaran')
                    ->default(\App\Models\TahunAjaran::where('status', 1)->value('id'))
                    ->required(),

                Forms\Components\Select::make('semester_id')
                    ->label('Semester')
                    ->disabled()
                    ->relationship('semester', 'nm_semester')
                    ->default(\App\Models\Semester::where('status', 1)->value('id'))
                    ->required(),
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            Split::make([
                // Kolom kiri — info utama jadwal
                TextColumn::make('hari')
                    ->label('Hari')
                    ->weight(FontWeight::Bold)
                    ->sortable(),

                TextColumn::make('jam_ke')
                    ->label('Jam Ke')
                    ->state(fn ($record) => is_array($record->jam_ke)
                        ? implode(', ', $record->jam_ke)
                        : $record->jam_ke
                    )
                    ->badge()
                    ->color('primary'),

                TextColumn::make('waktu_mengajar')
                    ->label('Waktu')
                    ->icon('heroicon-o-clock')
                    // ->color('primary')
                    ->weight(FontWeight::Medium)
                    ->sortable(),

                // Kolom tengah — kelas dan mapel
                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color(fn ($record) => match (true) {
                        Str::startsWith($record->kelas?->nama_kelas, ['VIIA', 'VIIB', 'VIIC', 'VIID']) => 'gray',
                        Str::startsWith($record->kelas?->nama_kelas, ['VIIIA', 'VIIIB', 'VIIIC', 'VIIID']) => 'info',
                        Str::startsWith($record->kelas?->nama_kelas, ['IXA', 'IXB', 'IXC', 'IXD']) => 'warning',
                        default => 'gray',
                    })
                    ->weight(FontWeight::SemiBold)
                    ->sortable(),

                TextColumn::make('mapel.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->mapel?->nama_mapel),

                // Kolom kanan — nama guru
                TextColumn::make('guru.nm_pegawai')
                    ->label('Guru Pengajar')
                    ->weight(FontWeight::Medium)
                    ->icon('heroicon-o-user')
                    ->searchable(),
            ]),
        ])
        ->filters([
        // 🔹 Filter Hari
        Tables\Filters\SelectFilter::make('hari')
            ->label('Filter Hari')
            ->options([
                'Senin' => 'Senin',
                'Selasa' => 'Selasa',
                'Rabu' => 'Rabu',
                'Kamis' => 'Kamis',
                'Jumat' => 'Jumat',
                'Sabtu' => 'Sabtu',
            ])
            ->searchable()
            ->preload(),

        // 🔹 Filter Kelas
        Tables\Filters\SelectFilter::make('kelas_id')
            ->label('Filter Kelas')
            ->relationship('kelas', 'nama_kelas')
            ->searchable()
            ->preload()
            ->indicator('Kelas'),            
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalMengajars::route('/'),
            'create' => Pages\CreateJadwalMengajar::route('/create'),
            'edit' => Pages\EditJadwalMengajar::route('/{record}/edit'),
        ];
    }
}
