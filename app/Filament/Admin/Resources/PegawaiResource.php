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
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PegawaiResource\Pages;
use App\Filament\Admin\Resources\PegawaiResource\RelationManagers\PosisiKepegawaianRelationManager;
// use App\Filament\Admin\Resources\PegawaiResource\RelationManagers;
// use App\Filament\Admin\Resources\PegawaiResource\RelationManagers\PosisiKepegawaianRelationManager;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Pengelolaan Pegawai';

    protected static ?string $navigationLabel = 'Data Pegawai';

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
                    // ->required()
                    ->maxLength(16)
                    ->minLength(16)
                    ->extraInputAttributes([
                        'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                        ])
                    ->validationMessages([
                        // 'required' => 'NIK tidak boleh kosong',
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
                // ->required()
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
                    // 'required' => 'NUPTK tidak boleh kosong',
                    'unique' => 'NUPTK sudah ada',
                    'numeric' => 'NUPTK harus angka',
                ]),
                Forms\Components\TextInput::make('npy')
                ->label('NPY')
                // ->required()
                ->numeric()
                ->unique(table: Pegawai::class, ignoreRecord: true)
                ->placeholder('Masukkan Nomor Pegawai Yayasan')
                ->maxLength(10)
                ->minLength(10)
                ->extraInputAttributes([
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                ])
                ->validationMessages([
                    'max' => 'NPY tidak boleh lebih dari 10 angka',
                    // 'required' => 'NPY tidak boleh kosong',
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
                ->default(function ($record) {
                    return $record?->foto_pegawai; // Ambil nilai dari model
                })
                ->afterStateUpdated(function ($state, $record, callable $set, callable $get) {
                    if ($state && $record && $record->foto_pegawai) {
                        Storage::disk('public')->delete($record->foto_pegawai);
                    }
                })
                ->deleteUploadedFileUsing(function ($record) {
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
                // Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
            
                        // Bagian kiri: Foto Pegawai
                        Tables\Columns\ImageColumn::make('foto_pegawai')
                            ->simpleLightbox()
                            ->label('Foto')
                            ->circular()
                            ->size(100)
                            ->grow(false)
                            ->defaultImageUrl(asset('images/no_pic.jpg')),
            
                        // Bagian tengah: Identitas & Status
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('nm_pegawai')
                                ->label('Nama Pegawai')
                                ->weight(FontWeight::Bold)
                                ->searchable()
                                ->sortable(),
            
                            Tables\Columns\TextColumn::make('nik')
                                ->searchable()
                                ->color('gray')
                                ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>NIK: " . ($state ?? '-') . "</small>")),
            
                            Tables\Columns\TextColumn::make('npy')
                                ->searchable()
                                ->color('gray')
                                ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>NPY: " . ($state ?? '-') . "</small>")),
            
                            Tables\Columns\TextColumn::make('nuptk')
                                ->searchable()
                                ->color('gray')
                                ->formatStateUsing(fn (?string $state): HtmlString => new HtmlString("<small>NUPTK: " . ($state ?? '-') . "</small>")),
            
                            // Badge Status
                            Tables\Columns\TextColumn::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => $state ? 'success' : 'danger')
                                ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif'),
                        ]),
            
                        // Bagian kanan: Kontak & Alamat
                        Tables\Columns\Layout\Stack::make([
                            Tables\Columns\TextColumn::make('alamat')
                                ->label('Alamat')
                                ->color('gray')
                                ->searchable()
                                ->formatStateUsing(fn (?string $state): string => $state ?? '-'),
            
                            Tables\Columns\TextColumn::make('user.email')
                                ->icon('heroicon-m-envelope')
                                ->label('Email')
                                ->color('gray')
                                ->searchable()
                                ->formatStateUsing(fn (?string $state): string => $state ?? '-'),
            
                            Tables\Columns\TextColumn::make('phone')
                                ->icon('heroicon-m-phone')
                                ->label('Telepon')
                                ->color('gray')
                                ->searchable()
                                ->formatStateUsing(fn (?string $state): string => $state ?? '-'),
                        ])->visibleFrom('md'),
            
                    ]),
                // ]),
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
                    ->modalHeading('Hapus Pegawai'),
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
            //
        ];
    }

    public static function getNavigationItems(): array
{
    if (auth()->user()?->pegawai) {
        $pegawaiId = auth()->user()->pegawai->id;

        return [
            \Filament\Navigation\NavigationItem::make()
                ->label('Data Diri')
                ->icon('heroicon-o-user')
                ->url(static::getUrl('view', ['record' => $pegawaiId])),
        ];
    }

    return parent::getNavigationItems();
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
