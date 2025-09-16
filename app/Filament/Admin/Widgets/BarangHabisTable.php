<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Atk;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BarangHabisTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Atk::query()->where('stock', '<=', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_atk')
                    ->label('Nama ATK')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable()
                    ->alignEnd(),
            ]);
    }
}
