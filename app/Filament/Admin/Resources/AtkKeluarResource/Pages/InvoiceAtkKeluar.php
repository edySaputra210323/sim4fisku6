<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;


use App\Filament\Admin\Resources\AtkKeluarResource;
use Filament\Resources\Pages\Page;
use Filament\Infolists;
use App\Models\AtkKeluar;

class InvoiceAtkKeluar extends Page
{
    protected static string $resource = AtkKeluarResource::class;

    protected static string $view = 'filament.admin.resources.atk-keluar-resource.pages.invoice-atk-keluar';
    // protected static string $view = 'filament.admin.resources.atk-keluar.invoice';

    public AtkKeluar $record;

    public function mount($record): void
    {
        $this->record = AtkKeluar::with(['pegawai', 'tahunAjaran', 'semester', 'details.atk'])->findOrFail($record);
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->dateTime('d F Y H:i'),

                        Infolists\Components\TextEntry::make('pegawai.nm_pegawai')
                            ->label('Pegawai Penerima'),

                        Infolists\Components\TextEntry::make('tahunAjaran.th_ajaran')
                            ->label('Tahun Ajaran'),

                        Infolists\Components\TextEntry::make('semester.nm_semester')
                            ->label('Semester'),

                        Infolists\Components\TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'verified' => 'success',
                                'canceled' => 'danger',
                                default => 'secondary',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Barang')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('details')
                            ->schema([
                                Infolists\Components\TextEntry::make('atk.nama_atk')
                                    ->label('Nama Barang'),
                                Infolists\Components\TextEntry::make('qty')
                                    ->label('Jumlah'),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }
}
