<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;


use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Stack;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Admin\Resources\PegawaiResource;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    public function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        Section::make('Profil Pegawai')
            ->schema([
                // Layout 2 kolom
                \Filament\Infolists\Components\Grid::make(2)
                    ->schema([
                        // Kolom 1: Foto
                        \Filament\Infolists\Components\ImageEntry::make('foto_pegawai')
                            ->label('Foto')
                            ->circular()
                            ->size(120)
                            ->columnSpan(1)
                            ->alignCenter()
                            ->defaultImageUrl(asset('images/no_pic.jpg')),

                        // Kolom 2: Data Utama
                        Section::make()
                        ->schema([
                            TextEntry::make('nm_pegawai')
                                ->label('Nama Pegawai')
                                ->weight('bold')
                                ->size('lg'),
                            TextEntry::make('status')
                                ->badge()
                                ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
                                ->color(fn ($state) => $state ? 'success' : 'danger'),
                            TextEntry::make('user.email')
                                ->label('Email')
                                ->icon('heroicon-m-envelope'),
                            TextEntry::make('phone')
                                ->label('Telepon')
                                ->icon('heroicon-m-phone'),
                        ])
                        ->columns(1), // biar turun ke bawah (stacked)
                    ]),
            ])
            ->columns(1), // section tetap 1 kolom biar foto & data sejajar
        

        Section::make('Informasi Lain')
            ->schema([
                \Filament\Infolists\Components\Grid::make(2)
                    ->schema([
                        \Filament\Infolists\Components\TextEntry::make('nik')->label('NIK'),
                        \Filament\Infolists\Components\TextEntry::make('npy')->label('NPY'),
                        \Filament\Infolists\Components\TextEntry::make('nuptk')->label('NUPTK'),
                        \Filament\Infolists\Components\TextEntry::make('jenis_kelamin')->label('Jenis Kelamin'),
                        \Filament\Infolists\Components\TextEntry::make('tempat_tanggal_lahir')->label('Tempat, Tanggal Lahir'),
                        \Filament\Infolists\Components\TextEntry::make('alamat')->label('Alamat')->columnSpan(2),
                    ]),
            ])
            ->columns(1),
    ]);
}

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
