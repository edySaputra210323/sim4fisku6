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
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\AbsensiHeaderResource\Pages;
use App\Filament\Admin\Resources\AbsensiHeaderResource\RelationManagers\AbsensiDetailRelationManager;

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
        ->columns([
            Tables\Columns\TextColumn::make('kelas.nama_kelas')
                ->label('Kelas')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('mapel.nama_mapel')
                ->label('Mapel')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('guru.nm_pegawai')
                ->label('Guru')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('tahunAjaran.th_ajaran')
                ->label('Tahun Ajaran'),

            Tables\Columns\TextColumn::make('semester.nm_semester')
                ->label('Semester'),

            Tables\Columns\TextColumn::make('tanggal')
                ->label('Tanggal')
                ->date(),

            Tables\Columns\TextColumn::make('pertemuan_ke')
                ->label('Pertemuan Ke'),

            Tables\Columns\TextColumn::make('kegiatan')
                ->label('Kegiatan')
                ->limit(20),
        ])
            ->filters([
                //
            ])
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
           AbsensiDetailRelationManager::class,
        //    AbsensiDetailRelationManager::class,
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
