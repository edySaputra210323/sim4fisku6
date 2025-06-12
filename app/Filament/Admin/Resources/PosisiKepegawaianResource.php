<?php

namespace App\Filament\Admin\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Jabatan;
use App\Models\Pegawai;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PosisiKepegawaian;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Resources\PosisiKepegawaianResource\Pages;
use App\Filament\Admin\Resources\PosisiKepegawaianResource\RelationManagers;

class PosisiKepegawaianResource extends Resource
{
    protected static ?string $model = PosisiKepegawaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengelolaan Pegawai';

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
                            ->options(Pegawai::all()->pluck('nm_pegawai', 'id'))
                            ->searchable(),
                Forms\Components\Select::make('jabatan_id')
                            ->label('Jabatan')
                            ->placeholder('Pilih Jabatan')
                            ->options(Jabatan::all()->pluck('nm_jabatan', 'id'))
                            ->searchable(),
                Forms\Components\Select::make('unit_id')
                            ->label('Unit')
                            ->placeholder('Pilih Unit')
                            ->options(Unit::all()->pluck('nm_unit', 'id'))
                            ->searchable(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('no_sk_pengangkatan')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Pengangkatan')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->validationMessages([
                        'required' => 'Tanggal Pengangkatan tidak boleh kosong',
                    ]),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Pengunduran Diri / Akhir Masa Jabatan')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pegawai_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jabatan_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('no_sk_pengangkatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
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
