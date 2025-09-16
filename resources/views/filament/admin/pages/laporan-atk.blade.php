<x-filament-panels::page>
    @if (! $this->hasActivePeriod())
        <x-filament::section>
            <x-slot name="heading">⚠️ Periode Tidak Aktif</x-slot>
            <p class="text-sm text-gray-600">
                Tidak ada <strong>Tahun Ajaran</strong> atau <strong>Semester</strong> yang aktif saat ini.<br>
                Silakan aktifkan periode terlebih dahulu untuk melihat laporan ATK.
            </p>
        </x-filament::section>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Barang Habis --}}
            <x-filament::section>
                <x-slot name="heading">Barang Habis</x-slot>
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left">Nama ATK</th>
                            <th class="text-right">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getBarangHabis() as $barang)
                            <tr>
                                <td>{{ $barang->nama_atk }}</td>
                                <td class="text-right">{{ $barang->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-filament::section>

            {{-- Barang Masuk --}}
            <x-filament::section>
                <x-slot name="heading">Barang Masuk (Terbaru)</x-slot>
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left">Nama ATK</th>
                            <th class="text-right">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getBarangMasuk() as $barang)
                            <tr>
                                <td>{{ $barang->nama_atk }}</td>
                                <td class="text-right">{{ $barang->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-filament::section>

            {{-- Barang Keluar --}}
            <x-filament::section>
                <x-slot name="heading">Barang Keluar (Terbaru)</x-slot>
                <table class="w-full text-sm">
                    <thead>
                        <tr>
                            <th class="text-left">Nama ATK</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getBarangKeluar() as $detail)
                            <tr>
                                <td>{{ $detail->atk->nama_atk ?? '-' }}</td>
                                <td class="text-right">{{ $detail->qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-gray-500">Tidak ada data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </x-filament::section>

            {{-- Top 5 ATK --}}
            <x-filament::section>
                <x-slot name="heading">Barang Paling Sering Diambil</x-slot>
                @livewire(\App\Filament\Admin\Widgets\TopAtkChart::class)
            </x-filament::section>

        </div>
    @endif
</x-filament-panels::page>
