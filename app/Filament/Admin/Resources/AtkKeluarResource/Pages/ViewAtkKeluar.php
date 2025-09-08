<?php

namespace App\Filament\Admin\Resources\AtkKeluarResource\Pages;

use App\Filament\Admin\Resources\AtkKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewAtkKeluar extends ViewRecord
{
    protected static string $resource = AtkKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
            ->label('Kembali ke Daftar Transaksi')
            ->icon('heroicon-o-arrow-left')
            ->color('info')
            ->url($this->getResource()::getUrl('index')),
        ];
    }

    public function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Section::make('🧾 Nota Transaksi ATK')
                    ->description('Detail informasi transaksi pengambilan ATK')
                    ->schema([
                        TextEntry::make('id')
                            ->label('No. Transaksi')
                            ->badge()
                            ->color('primary'),

                        TextEntry::make('tanggal')
                            ->label('Tanggal Transaksi')
                            ->dateTime('d F Y H:i'),

                        TextEntry::make('pegawai.nm_pegawai')
                            ->label('Penerima')
                            ->icon('heroicon-o-user'),

                        TextEntry::make('tahunAjaran.th_ajaran')
                            ->label('Tahun Ajaran'),

                        TextEntry::make('semester.nm_semester')
                            ->label('Semester'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'verified' => 'success',
                                'canceled' => 'danger',
                                default => 'secondary',
                            }),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('📦 Detail Barang')
                    ->schema([
                        RepeatableEntry::make('details')
                            ->schema([
                                TextEntry::make('atk.nama_atk')
                                    ->label('Nama Barang')
                                    ->weight('bold'),

                                TextEntry::make('qty')
                                    ->label('Jumlah')
                                    ->suffix(fn ($record) => $record->atk?->satuan ?? ''),
                            ])
                            ->columns(2)
                            ->contained(false) // biar tampilannya kayak tabel
                            ->grid(2),
                    ])
                    ->collapsed(false),

                Section::make()
                    ->schema([
                        TextEntry::make('details_sum')
                            ->label('Total Item')
                            ->state(fn ($record) => $record->details->sum('qty'))
                            ->numeric()
                            ->suffix(' pcs')
                            ->weight('bold')
                            ->color('primary'),
                    ]),
            ]);
    }
}
