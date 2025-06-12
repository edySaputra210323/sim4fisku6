<?php

namespace App\Filament\Admin\Resources;

use stdClass;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PosisiKepegawaian;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PosisiKepegawaianResource\Pages;
use App\Filament\Admin\Resources\PosisiKepegawaianResource\RelationManagers;

class PosisiKepegawaianResource extends Resource
{
    protected static ?string $model = PosisiKepegawaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengelolaan Pegawai';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Posisi Kepegawaian';

    protected static ?string $modelLabel = 'Posisi Kepegawaian';

    protected static ?string $pluralModelLabel = 'Posisi Kepegawaian';

    protected static ?string $slug = 'posisi-kepegawaian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                Forms\Components\Select::make('pegawai_id')
                            ->label('Nama Pegawai')
                            ->placeholder('Pilih Nama Pegawai')
                            ->required()
                            ->options(Pegawai::all()->pluck('nm_pegawai', 'id'))
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Nama Pegawai tidak boleh kosong',
                            ]),
                Forms\Components\Select::make('jabatan_id')
                            ->label('Jabatan')
                            ->placeholder('Pilih Jabatan')
                            ->required()
                            ->options(Jabatan::all()->pluck('nm_jabatan', 'id'))
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Jabatan tidak boleh kosong',
                            ]),
                Forms\Components\Select::make('unit_id')
                            ->label('Unit')
                            ->placeholder('Pilih Unit')
                            ->required()
                            ->options(Unit::all()->pluck('nm_unit', 'id'))
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Unit tidak boleh kosong',
                            ]),
                Forms\Components\Select::make('status')
                    ->label('Status Kepegawaian')
                    ->native(false)
                    ->required()
                    ->options([
                        'permanent' => 'Permanent',
                        'contract' => 'Contract',
                        'honorary' => 'Honorary',
                    ])
                    ->validationMessages([
                        'required' => 'Status Kepegawaian tidak boleh kosong',
                    ]),
                Forms\Components\TextInput::make('no_sk_pengangkatan')
                    ->maxLength(255)
                    ->required()
                    ->validationMessages([
                        'required' => 'Nomor SK Pengangkatan tidak boleh kosong',
                    ]),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Pengangkatan')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y') // Format tanggal Indonesia
                    ->format('d/m/Y') // Pastikan format input juga d/m/Y
                    ->validationMessages([
                        'required' => 'Tanggal Pengangkatan tidak boleh kosong',
                    ]),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Pengunduran Diri / Akhir Masa Jabatan')
                    ->native(false)
                    ->displayFormat('d/m/Y') // Format tanggal Indonesia
                    ->format('d/m/Y') // Pastikan format input juga d/m/Y
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordAction(null)
            ->recordUrl(null)
            ->extremePaginationLinks()
            ->paginated([5, 10, 20, 50])
            ->defaultPaginationPageOption(10)
            ->striped()
            ->recordClasses(function () {
                $classes = 'table-vertical-align-top ';
                return $classes;
            })
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No')
                    ->width('1%')
                    ->alignCenter()
                    ->state(
                        static function (HasTable $livewire, stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                (intval($livewire->getTableRecordsPerPage()) * (
                                    intval($livewire->getTablePage()) - 1
                                ))
                            );
                        }
                    ),
                Tables\Columns\TextColumn::make('pegawai.nm_pegawai')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jabatan.nm_jabatan')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.kode_unit')
                    ->label('Unit')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state)), // Ubah ke format kapital pertama
                Tables\Columns\TextColumn::make('no_sk_pengangkatan')
                    ->searchable()
                    ->sortable()
                    ->label('No SK Pengangkatan'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y') // Format tanggal Indonesia
                    ->color('success')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date('d/m/Y') // Format tanggal Indonesia
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('d/m/Y'))
                    ->sortable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosisiKepegawaians::route('/'),
            'create' => Pages\CreatePosisiKepegawaian::route('/create'),
            'view' => Pages\ViewPosisiKepegawaian::route('/{record}'),
            'edit' => Pages\EditPosisiKepegawaian::route('/{record}/edit'),
        ];
    }
}
