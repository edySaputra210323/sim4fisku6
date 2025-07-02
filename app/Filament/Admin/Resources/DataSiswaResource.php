<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\DataSiswa;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use App\Imports\SiswaImportProcessor;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Grid;
use Illuminate\Database\Eloquent\Builder;
use EightyNine\ExcelImport\ExcelImportAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\DataSiswaResource\Pages;
use App\Filament\Admin\Resources\DataSiswaResource\RelationManagers;

class DataSiswaResource extends Resource
{
    protected static ?string $model = DataSiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Siswa';

    protected static ?string $modelLabel = 'Data Siswa';

    protected static ?string $pluralModelLabel = 'Data Siswa';

    protected static ?string $slug = 'data-siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Section::make()
                        ->schema([
                            Forms\Components\TextInput::make('nama_siswa')
                    ->required()
                    ->maxLength(100)
                    ->validationMessages([
                        'required' => 'Nama siswa wajib diisi',
                    ])
                    ->columnSpan('full'),
                Forms\Components\TextInput::make('nis')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('nisn')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('no_hp')
                    ->maxLength(15),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('agama')
                    ->maxLength(50),
                Forms\Components\TextInput::make('jenis_kelamin')
                    ->required(),
                Forms\Components\TextInput::make('tempat_lahir')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->required(),
                Forms\Components\TextInput::make('negara')
                    ->maxLength(50),
                Forms\Components\TextInput::make('provinsi')
                    ->maxLength(50),
                Forms\Components\TextInput::make('kabupaten')
                    ->maxLength(50),
                Forms\Components\TextInput::make('kecamatan')
                    ->maxLength(50),
                Forms\Components\TextInput::make('kelurahan')
                    ->maxLength(50),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(100),
                Forms\Components\TextInput::make('rt')
                    ->maxLength(10),
                Forms\Components\TextInput::make('rw')
                    ->maxLength(10),
                Forms\Components\TextInput::make('kode_pos')
                    ->maxLength(10),
                Forms\Components\TextInput::make('yatim_piatu'),
                Forms\Components\TextInput::make('penyakit')
                    ->maxLength(100),
                Forms\Components\TextInput::make('jumlah_saudara')
                    ->maxLength(10),
                Forms\Components\TextInput::make('anak_ke')
                    ->maxLength(10),
                Forms\Components\TextInput::make('dari_bersaudara')
                    ->maxLength(10),
                Forms\Components\TextInput::make('jarak_tempuh_id')
                    ->numeric(),
                Forms\Components\TextInput::make('transport_id')
                    ->numeric(),
                Forms\Components\TextInput::make('angkatan')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('tanggal_masuk'),
                Forms\Components\DatePicker::make('tanggal_keluar'),
                Forms\Components\TextInput::make('lanjut_sma_dimana')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status_siswa_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nm_ayah')
                    ->maxLength(100),
                Forms\Components\TextInput::make('pendidikan_ayah_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaan_ayah_id')
                    ->numeric(),
                Forms\Components\TextInput::make('penghasilan_ayah_id')
                    ->numeric(),
                Forms\Components\TextInput::make('no_hp_ayah')
                    ->maxLength(15),
                Forms\Components\TextInput::make('nm_ibu')
                    ->maxLength(100),
                Forms\Components\TextInput::make('pendidikan_ibu_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaan_ibu_id')
                    ->numeric(),
                Forms\Components\TextInput::make('penghasilan_ibu_id')
                    ->numeric(),
                Forms\Components\TextInput::make('no_hp_ibu')
                    ->maxLength(15),
                Forms\Components\TextInput::make('nm_wali')
                    ->maxLength(100),
                Forms\Components\TextInput::make('pendidikan_wali_id')
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaan_wali_id')
                    ->numeric(),
                Forms\Components\TextInput::make('penghasilan_wali_id')
                    ->numeric(),
                Forms\Components\TextInput::make('no_hp_wali')
                    ->maxLength(15),
                Forms\Components\TextInput::make('user_id')
                    ->numeric(),
                        ])->columnSpan(2)->columns(2),
                Section::make()
                    ->schema([
                        Forms\Components\FileUpload::make('foto_siswa')
                            ->label('Foto Siswa')
                            ->disk('public')
                            ->directory('siswa')
                            ->acceptedFileTypes(['image/jpeg', 'image/png']),
                        Forms\Components\FileUpload::make('upload_ijazah_sd')
                            ->label('Ijazah SD')
                            ->disk('public')
                            ->required()
                            ->directory('ijazah_sd')
                            ->acceptedFileTypes(['application/pdf'])
                            ->validationMessages([
                                'required' => 'Ijazah SD wajib diisi',
                            ])
                    ])->columnSpan(1)->columns(1),
            ])->columns(3);
    }
    public static function table(Table $table): Table
    {
        return $table
        ->extremePaginationLinks()
        ->recordUrl(null)
        ->paginated([5, 10, 20, 50])
        ->defaultPaginationPageOption(10)
        ->striped()
        ->recordClasses(function () {
            $classes = 'table-vertical-align-top ';
            return $classes;
        })
            ->columns([
                Tables\Columns\ImageColumn::make('foto_siswa')
                    ->simpleLightbox()
                    ->label('Foto')
                    ->circular()
                    ->size(60)
                    ->grow(false)
                    ->defaultImageUrl(asset('images/no_pic.jpg')),
                    Tables\Columns\TextColumn::make('nama_siswa')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->label('Nama Siswa')
                    ->description(function ($record) {
                        $data = '';
                        if (!empty($record->nis)) {
                            $data .= '<small>NIS : ' . $record->nis . '</small>';
                        }
                        if (!empty($record->nisn)) {
                            if ($data != '')
                                $data .= '<br>';
                            $data .= '<small>NISN : ' . $record->nisn . '</small>';
                        }
                        if (!empty($record->nik)) {
                            if ($data != '')
                                $data .= '<br>';
                            $data .= '<small>NIK : ' . $record->nik . '</small>';
                        }
                        return new HtmlString($data);
                    }),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->searchable()
                    ->label('JK'),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('negara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('provinsi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rw')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_pos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('yatim_piatu'),
                Tables\Columns\TextColumn::make('penyakit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_saudara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('anak_ke')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dari_bersaudara')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jarak_tempuh_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transport_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('angkatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lanjut_sma_dimana')
                    ->searchable(),
                Tables\Columns\TextColumn::make('upload_ijazah_sd')
                    ->searchable(),
                Tables\Columns\TextColumn::make('foto_siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_siswa_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nm_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan_ayah_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pekerjaan_ayah_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penghasilan_ayah_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp_ayah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nm_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan_ibu_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pekerjaan_ibu_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penghasilan_ibu_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nm_wali')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan_wali_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pekerjaan_wali_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('penghasilan_wali_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp_wali')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListDataSiswas::route('/'),
            'create' => Pages\CreateDataSiswa::route('/create'),
            'view' => Pages\ViewDataSiswa::route('/{record}'),
            'edit' => Pages\EditDataSiswa::route('/{record}/edit'),
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
