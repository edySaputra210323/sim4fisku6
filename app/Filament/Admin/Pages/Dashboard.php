<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Orion\FilamentGreeter\GreeterWidget;
use App\Filament\Admin\Widgets\PekerjaanOrtuChart;
// use App\Filament\Admin\Widgets\StatistikSiswaTable;
use App\Filament\Admin\Widgets\KabupatenSiswaChart;
use App\Filament\Admin\Widgets\SiswaPerAngkatanChart;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            // Ini akan muncul setelah Greeter (karena sort Greeter -1)
            GreeterWidget::class,
            SiswaPerAngkatanChart::class,
            // StatistikSiswaTable::class,
            PekerjaanOrtuChart::class,
            KabupatenSiswaChart::class,
            
        ];
    }
}
