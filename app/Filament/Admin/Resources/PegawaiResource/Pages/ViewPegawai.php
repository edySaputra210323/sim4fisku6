<?php

namespace App\Filament\Admin\Resources\PegawaiResource\Pages;


use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Stack;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Tabs;
use App\Filament\Admin\Resources\PegawaiResource;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;
use Filament\Support\Enums\FontWeight;

class ViewPegawai extends ViewRecord
{
    protected static string $resource = PegawaiResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Profil Pegawai')
                ->schema([
                    Split::make([
                        // KIRI: foto (tidak grow, dan center di kolomnya)
                        Section::make()
                            ->schema([
                                ImageEntry::make('foto_pegawai')
                                    ->label(false)
                                    ->circular()
                                    ->size(150)
                                    ->defaultImageUrl(asset('images/no_pic.jpg')),
                                TextEntry::make('status_pegawai.nama_status')
                                    ->badge()
                                    ->label('Status')
                                    ->color(fn ($record) => $record->status_pegawai?->warna ?? 'gray')
                                    ->formatStateUsing(fn ($record) => $record->status_pegawai?->nama_status ?? '-'),
                            ])
                            ->grow(false)
                            ->extraAttributes([
                                // jadikan container ini flex untuk center langsung
                                'class' => 'flex items-center justify-center',
                            ]),

                        // KANAN: data (ditumpuk secara vertikal)
                        Section::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextEntry::make('nm_pegawai')
                                            ->label('Nama Pegawai')
                                            ->weight('bold')
                                            ->size('lg'),
                                        TextEntry::make('bidang_studi')
                                            ->label('Bidang Studi')
                                            ->weight('bold')
                                            ->color('gray')
                                            ->size('md'),
                                        TextEntry::make('user.email')
                                            ->label(false)
                                            ->color('gray')
                                            ->icon('heroicon-m-envelope'),
                                        TextEntry::make('phone')
                                            ->label(false)
                                            ->color('gray')
                                            ->icon('heroicon-m-phone'),
                                    ]),
                            ]),
                    ])
                        ->from('md') // aktif sebagai split mulai breakpoint md
                        ->extraAttributes([
                            // tambahkan tailwind classes: vertical center + jarak antar kolom
                            'class' => 'items-center gap-6',
                        ]),
                ]),

            Section::make('Informasi Lain')
                ->schema([
                    \Filament\Infolists\Components\Grid::make([
                        'default' => 1, // di mobile → 1 kolom
                        'md' => 2,      // mulai layar medium (tablet ke atas) → 2 kolom
                    ])
                        ->schema([
                            \Filament\Infolists\Components\TextEntry::make('nik')
                                ->label('NIK')
                                ->color('gray'),
                            \Filament\Infolists\Components\TextEntry::make('npy')
                                ->label('Nomor Pegawai Yayasan')
                                ->color('gray'),
                            \Filament\Infolists\Components\TextEntry::make('nuptk')
                                ->label('NUPTK')
                                ->color('gray'),
                            \Filament\Infolists\Components\TextEntry::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'L' => 'Laki-laki',
                                    'P' => 'Perempuan',
                                    default => '-',
                                })
                                ->color('gray'),
                            \Filament\Infolists\Components\TextEntry::make('tempat_tanggal_lahir')
                                ->label('Tempat, Tanggal Lahir')
                                ->color('gray'),
                            \Filament\Infolists\Components\TextEntry::make('alamat')
                                ->label('Alamat')
                                ->color('gray')
                                ->columnSpanFull(), // otomatis full width di grid
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
