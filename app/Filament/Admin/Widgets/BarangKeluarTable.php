<?php

namespace App\Filament\Admin\Widgets;

use App\Models\DetailAtkKeluar;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BarangKeluarTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DetailAtkKeluar::query()
                    ->with('atk')
                    ->orderByDesc('created_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('atk.nama_atk')
                    ->label('Nama ATK')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('qty')
                    ->label('Jumlah')
                    ->sortable()
                    ->alignEnd(),
            ]);
    }
}
