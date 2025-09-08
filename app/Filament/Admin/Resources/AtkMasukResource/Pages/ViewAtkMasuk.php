<?php

namespace App\Filament\Admin\Resources\AtkMasukResource\Pages;

use Filament\Actions;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Admin\Resources\AtkMasukResource;
use Filament\Infolists\Components\RepeatableEntry;

class ViewAtkMasuk extends ViewRecord
{
    protected static string $resource = AtkMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('downloadPdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    $record = $this->record;

                    $pdf = Pdf::loadView('pdf.atk-masuk', [
                        'record' => $record,
                        'details' => $record->details,
                        'grand_total' => $record->details->sum('total_harga'),
                    ]);

                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        'Transaksi-' . $record->nomor_nota . '.pdf'
                    );
                }),
        ];
    }

    public function infolist(\Filament\Infolists\Infolist $infolist): \Filament\Infolists\Infolist
    {
        return $infolist->schema([
            Section::make('Informasi Transaksi')
                ->schema([
                    TextEntry::make('tanggal')->date()->label('Tanggal'),
                    TextEntry::make('nomor_nota')->label('Nomor Nota'),
                ])->columns(2),

            Section::make('Detail Barang Masuk')
                ->schema([
                    RepeatableEntry::make('details')
                        ->schema([
                            TextEntry::make('atk.nama_atk')->label('Nama Barang'),
                            TextEntry::make('qty')->suffix('unit')->label('Qty'),
                            TextEntry::make('harga_satuan')->money('idr')->label('Harga Satuan'),
                            TextEntry::make('total_harga')->money('idr')->label('Total Harga'),
                        ])
                        ->columns(4),
                ]),

            Section::make('Ringkasan')
                ->schema([
                    TextEntry::make('grand_total')
                        ->label('Total Bayar')
                        ->money('idr')
                        ->getStateUsing(fn ($record) => $record->details->sum('total_harga')),
                ]),
        ]);
    }
}
