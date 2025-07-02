<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use stdClass;
use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Clusters\Master;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\UnitResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\UnitResource\RelationManagers;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Master::class;

    // protected static ?string $navigationGroup = 'Pegawai';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Unit';

    protected static ?string $modelLabel = 'Unit';

    protected static ?string $pluralModelLabel = 'Unit';

    protected static ?string $slug = 'unit';

    public static function form(Form $form): Form
    {
        return $form
            ->inlineLabel()
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                Forms\Components\TextInput::make('nm_unit')
                    ->maxLength(255)
                    ->label('Nama Unit')
                    ->placeholder('Contoh: Unit Sekolah Menengah Pertama Islam Terpadu')
                    ->required()
                    // ->extraInputAttributes([
                    //     'oninput' => 'this.value = this.value.toUpperCase()',
                    // ])
                ->validationMessages([
                    'required' => ' Nama unit tidak boleh kosong',
                    ]),
                Forms\Components\TextInput::make('kode_unit')
                    ->maxLength(255)
                    ->label('Kode Unit')
                    ->placeholder('Contoh: SMPIT')
                    ->required()
                    // ->extraInputAttributes([
                    //     'oninput' => 'this.value = this.value.toUpperCase()',
                    // ])
                ->validationMessages([
                    'required' => 'Kode unit tidak boleh kosong',
                    ]),
                Forms\Components\Textarea::make('deskripsi')
                    ->rows(3)
                    ->label('Deskripsi')
                    ->placeholder('Deskripsi unit')
                    ->required()
                    ->validationMessages([
                        'required' => 'Deskripsi unit tidak boleh kosong',
                    ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
            return $query
            ->orderBy('nm_unit', 'asc');
            })
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
        // ->groups([
        //     Tables\Grouping\Group::make('nm_jabatan')
        //         ->label('Jabatan'),
        // ])
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
                Tables\Columns\TextColumn::make('nm_unit')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Unit'),
                Tables\Columns\TextColumn::make('kode_unit')
                    ->searchable()
                    ->sortable()
                    ->label('Kode Unit'),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->searchable()
                    ->sortable()
                    ->label('Deskripsi'),
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
                    ->modalHeading('Hapus Jabatan'),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'view' => Pages\ViewUnit::route('/{record}'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
