<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use stdClass;
use Filament\Forms;
use Filament\Tables;
use App\Models\Kelas;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Clusters\Master;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Admin\Clusters\Master\Resources\KelasResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\KelasResource\RelationManagers;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Master::class;

    protected static ?string $navigationGroup = 'Master Siswa';

    protected static ?string $navigationLabel = 'Kelas';

    protected static ?string $modelLabel = 'Kelas';

    protected static ?string $pluralModelLabel = 'Kelas';

    protected static ?string $slug = 'kelas';

    public static function form(Form $form): Form
    {
        $form->inlineLabel();
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kelas')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kelas')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'unique' => 'Kelas sudah ada, gunakan nama kelas yang lain',
                                'required' => 'Nama Kelas tidak boleh kosong',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        $table->modifyQueryUsing(function (Builder $query) {
            return $query->orderBy('nama_kelas', 'asc');
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
        });
        return $table
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
                Tables\Columns\TextColumn::make('nama_kelas')
                    ->searchable(),
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'view' => Pages\ViewKelas::route('/{record}'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
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
