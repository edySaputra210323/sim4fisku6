<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PegawaiResource\Pages;
use App\Filament\Admin\Resources\PegawaiResource\RelationManagers;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Pegawai';

    protected static ?string $modelLabel = 'Pegawai';

    protected static ?string $pluralModelLabel = 'Pegawai';

    protected static ?string $slug = 'pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
            Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->placeholder('Nomor Induk Kependudukan')
                    ->numeric()
                    ->unique(table: Pegawai::class, ignoreRecord: true)
                    ->required()
                    ->maxLength(16)
                    ->minLength(16)
                    ->extraInputAttributes([
                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                        ])
                    ->validationMessages([
                        'required' => 'NIK tidak boleh kosong',
                        'max' => 'NIK tidak boleh lebih dari 16 angka',
                        'numeric' => 'NIK harus angka',
                        'unique' => 'NIK sudah ada',
                        ]),
            Forms\Components\TextInput::make('nm_pegawai')
                ->label('Nama')
                ->placeholder('masukkan nama lengkap dan gelar')
                ->maxLength(100)
                ->required()
                ->validationMessages([
                    'required' => 'Nama Lengkap tidak boleh kosong',
                    'max' => 'Nama Lengkap tidak boleh lebih dari 100 karakter',
                ]),
            Forms\Components\TextInput::make('tempat_lahir')
                ->label('Tempat Lahir')
                ->placeholder('masukkan tempat lahir')
                ->maxLength(100)
                ->required()
                ->validationMessages([
                    'required' => 'Tempat Lahir tidak boleh kosong',
                    'max' => 'Tempat Lahir tidak boleh lebih dari 100 karakter',
                ]),
            Forms\Components\DatePicker::make('tgl_lahir')
                ->label('Tanggal Lahir')
                ->required()
                ->native(false)
                ->displayFormat('d/m/Y')
                ->validationMessages([
                    'required' => 'Tanggal Lahir tidak boleh kosong',
                ]),
            Forms\Components\Select::make('jenis_kelamin')
                ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                ->label('JK')
                ->required(),
            Forms\Components\TextInput::make('alamat')
                ->label('Alamat'),
            Forms\Components\TextInput::make('phone')
                ->label('Nomor Telepon')
                ->maxLength(15)
                ->minLength(10)
                ->numeric()
                ->extraInputAttributes([
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                ])
                ->validationMessages([
                    'max' => 'Nomor Telepon tidak boleh lebih dari 15 angka',
                    'min' => 'Nomor Telepon tidak boleh kurang dari 10 angka',
                    'numeric' => 'Nomor Telepon harus angka',
                ]),
            Forms\Components\TextInput::make('nuptk')
                ->label('NUPTK')
                ->required()
                ->numeric()
                ->unique(table: Pegawai::class, ignoreRecord: true)
                ->placeholder('Masukkan Nomor Unik Pendidik dan Tenaga Kependidikan')
                ->maxLength(16)
                ->minLength(16)
                ->extraInputAttributes([
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                ])
                ->validationMessages([
                    'max' => 'NUPTK tidak boleh lebih dari 16 karakter',
                    'required' => 'NUPTK tidak boleh kosong',
                    'unique' => 'NUPTK sudah ada',
                    'numeric' => 'NUPTK harus angka',
                ]),
                Forms\Components\TextInput::make('npy')
                ->label('NPY')
                ->required()
                ->numeric()
                ->unique(table: Pegawai::class, ignoreRecord: true)
                ->placeholder('Masukkan Nomor Pegawai Yayasan')
                ->maxLength(7)
                ->minLength(7)
                ->extraInputAttributes([
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                ])
                ->validationMessages([
                    'max' => 'NPY tidak boleh lebih dari 7 angka',
                    'required' => 'NPY tidak boleh kosong',
                    'unique' => 'NPY sudah ada',
                    'numeric' => 'NPY harus angka',
                ]),
            Forms\Components\Select::make('status')
                ->options([
                    true => 'Aktif',
                    false => 'Nonaktif',
                ])
                ->label('Status')
                ->required(),
            Forms\Components\FileUpload::make('foto_pegawai')
                ->image(['jpg', 'png'])
                ->label('Foto Pegawai')
                ->disk('public')
                ->minSize(50)
                ->maxSize(5120)
                ->openable()
                ->directory('pegawai_photos')
                ->removeUploadedFileButtonPosition('right')
                ->visibility('public')
                ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->afterStateUpdated(function ($state, $record, callable $set, callable $get) {
                                // Hapus file lama kalo upload file baru
                                if ($state && $record && $record->foto_pegawai) {
                                    Storage::disk('public')->delete($record->foto_pegawai);
                                }
                            })
                            ->deleteUploadedFileUsing(function ($record) {
                                // Hapus file kalo tombol remove diklik
                                if ($record && $record->foto_pegawai) {
                                    Storage::disk('public')->delete($record->foto_pegawai);
                                    $record->foto_pegawai = null;
                                    $record->save();
                                }
                            }),
            Forms\Components\Toggle::make('create_user_account')
                ->label('Buat Akun Pengguna')
                ->reactive()
                ->default(false),
            Forms\Components\TextInput::make('user_email')
                ->email()
                ->required(fn ($get) => $get('create_user_account'))
                ->unique(table: \App\Models\User::class, column: 'email')
                ->label('Email Pengguna')
                ->visible(fn ($get) => $get('create_user_account')),
            Forms\Components\TextInput::make('username_pegawai')
                ->placeholder('Masukkan username')
                ->required(fn ($get) => $get('create_user_account'))
                ->unique(table: \App\Models\User::class, column: 'username')
                ->label('username')
                ->visible(fn ($get) => $get('create_user_account')),
            Forms\Components\TextInput::make('password')
                ->password()
                ->required(fn ($get) => $get('create_user_account'))
                ->label('Kata Sandi')
                ->visible(fn ($get) => $get('create_user_account')),
            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->required(fn ($get) => $get('create_user_account'))
                ->same('password')
                ->label('Konfirmasi Kata Sandi')
                ->visible(fn ($get) => $get('create_user_account'))
                ->validationMessages([
                        'required' => 'Konfirmasi kata sandi wajib diisi',
                        'same' => 'Konfirmasi kata sandi tidak cocok',
                        ]),
            
            
            ])
                ]);
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
                Tables\Columns\ImageColumn::make('foto_pegawai')
                    ->simpleLightbox()
                    ->label('Foto')
                    ->circular()
                    ->size(60)
                    ->grow(false)
                    ->defaultImageUrl(asset('images/no_pic.jpg')),
                // Tables\Columns\ImageColumn::make('foto_pegawai')    
                //     ->label('Foto')
                //     ->circular()
                //     ->size(80)
                //     ->grow(false)
                //     ->defaultImageUrl(asset('storage/images/no_pic.png')),
                Tables\Columns\TextColumn::make('nm_pegawai')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->label('Nama Pegawai')
                    ->description(function ($record) {
                        $data = '';
                        if (!empty($record->nik)) {
                            $data .= '<small>NIK : ' . $record->nik . '</small>';
                        }
                        if (!empty($record->npy)) {
                            if ($data != '')
                                $data .= '<br>';
                            $data .= '<small>NPY : ' . $record->npy . '</small>';
                        }
                        if (!empty($record->nuptk)) {
                            if ($data != '')
                                $data .= '<br>';
                            $data .= '<small>Nuptk : ' . $record->nuptk . '</small>';
                        }
                        return new HtmlString($data);
                    }),
                Tables\Columns\TextColumn::make('tempat_tanggal_lahir')
                    ->label('Tempat, Tanggal Lahir'),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('JK'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
                    ->modalHeading('Hapus Bank'),
                Tables\Actions\ViewAction::make()
                ->iconButton()
                    ->color('primary')
                    ->icon('heroicon-m-eye'),
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
            PosisiKepegawaianRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'view' => Pages\ViewPegawai::route('/{record}'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
