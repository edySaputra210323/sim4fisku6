<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Jabatan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Clusters\Master;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\JabatanResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\JabatanResource\RelationManagers;

class JabatanResource extends Resource
{
    protected static ?string $model = Jabatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Master::class;

    // protected static ?string $navigationGroup = 'Pegawai';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Jabatan';

    protected static ?string $modelLabel = 'Jabatan';

    protected static ?string $pluralModelLabel = 'Jabatan';

    protected static ?string $slug = 'jabatan';

    public static function form(Form $form): Form
    {
        return $form
            ->inlineLabel()
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                Forms\Components\TextInput::make('nm_jabatan')
                    ->label('Jabatan')
                    ->placeholder('Contoh: Wali Kelas IX A')
                    ->required()
                    // ->extraInputAttributes([
                    //     'oninput' => 'this.value = this.value.toUpperCase()',
                    // ])
                ->validationMessages([
                    'required' => 'Jabatan tidak boleh kosong',
                    ])
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            return $query
                ->orderBy('nm_jabatan', 'asc');
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
                Tables\Columns\TextColumn::make('nm_jabatan')
                    ->searchable()
                    ->sortable()
                    ->label('Jabatan'),
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
            'index' => Pages\ListJabatans::route('/'),
            'create' => Pages\CreateJabatan::route('/create'),
            'view' => Pages\ViewJabatan::route('/{record}'),
            'edit' => Pages\EditJabatan::route('/{record}/edit'),
        ];
    }
}
