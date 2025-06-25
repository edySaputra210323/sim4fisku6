<?php

namespace App\Filament\Admin\Clusters\Master\Resources;

use App\Filament\Admin\Clusters\Master;
use App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource\Pages;
use App\Filament\Admin\Clusters\Master\Resources\JarakTempuhResource\RelationManagers;
use App\Models\JarakTempuh;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JarakTempuhResource extends Resource
{
    protected static ?string $model = JarakTempuh::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $cluster = Master::class;

    protected static ?string $navigationLabel = 'Jarak Tempuh';

    protected static ?string $modelLabel = 'Jarak Tempuh';

    protected static ?string $pluralModelLabel = 'Jarak Tempuh';

    protected static ?string $slug = 'jarak-tempuh';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                Forms\Components\TextInput::make('nama_jarak_tempuh')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('kode_jarak_tempuh')
                    ->required()
                    ->maxLength(50),
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
            return $query
                ->orderBy('nama_jarak_tempuh', 'asc');
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
            ->columns([
                Tables\Columns\TextColumn::make('nama_jarak_tempuh')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_jarak_tempuh')
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
            'index' => Pages\ListJarakTempuhs::route('/'),
            'create' => Pages\CreateJarakTempuh::route('/create'),
            'view' => Pages\ViewJarakTempuh::route('/{record}'),
            'edit' => Pages\EditJarakTempuh::route('/{record}/edit'),
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
