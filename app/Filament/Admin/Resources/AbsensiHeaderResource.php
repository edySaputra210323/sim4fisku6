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

            Forms\Components\Select::make('pegawai_id')
                ->label('Guru')
                ->relationship('guru', 'nm_pegawai')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(fn ($record) => $record !== null),

            Forms\Components\Select::make('tahun_ajaran_id')
                ->label('Tahun Ajaran')
                ->relationship('tahunAjaran', 'th_ajaran')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(fn ($record) => $record !== null),

            Forms\Components\Select::make('semester_id')
                ->label('Semester')
                ->relationship('semester', 'nm_semester')
                ->searchable()
                ->preload()
                ->required()
                ->disabled(fn ($record) => $record !== null),

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
                ->date(),
    
            Tables\Columns\TextColumn::make('guru.nm_pegawai')
                ->label('Guru')
                ->sortable()
                ->searchable(),
    
            Tables\Columns\TextColumn::make('kelas.nama_kelas')
                ->label('Kelas')
                ->sortable()
                ->searchable(),
    
            Tables\Columns\TextColumn::make('total_siswa')
                ->label('Total Siswa')
                ->state(fn ($record) => $record->total_siswa)
                // ->badge()
                ->weight(FontWeight::Bold),
    
            Tables\Columns\TextColumn::make('hadir_count')
                ->label('Hadir')
                ->badge()
                ->color('success'),
    
            Tables\Columns\TextColumn::make('sakit_count')
                ->label('Sakit')
                ->badge()
                ->color('warning'),
    
            Tables\Columns\TextColumn::make('izin_count')
                ->label('Izin')
                ->badge()
                ->color('info'),
    
            Tables\Columns\TextColumn::make('alpa_count')
                ->label('Alpa')
                ->badge()
                ->color('danger'),
        ])
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
