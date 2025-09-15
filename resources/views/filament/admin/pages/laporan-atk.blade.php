<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Barang Habis --}}
        <x-filament::section>
            <x-slot name="heading">📦 Barang Habis</x-slot>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Nama ATK</th>
                        <th class="text-right">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getBarangHabis() as $barang)
                        <tr>
                            <td>{{ $barang->nama_atk }}</td>
                            <td class="text-right">{{ $barang->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>

        {{-- Barang Masuk --}}
        <x-filament::section>
            <x-slot name="heading">📥 Barang Masuk (Terbaru)</x-slot>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Nama ATK</th>
                        <th class="text-right">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getBarangMasuk() as $barang)
                        <tr>
                            <td>{{ $barang->nama_atk }}</td>
                            <td class="text-right">{{ $barang->stock }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>

        {{-- Barang Keluar --}}
        <x-filament::section>
            <x-slot name="heading">📤 Barang Keluar (Terbaru)</x-slot>
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Nama ATK</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getBarangKeluar() as $detail)
                        <tr>
                            <td>{{ $detail->atk->nama_atk ?? '-' }}</td>
                            <td class="text-right">{{ $detail->qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>

        {{-- Top 5 ATK --}}
        <x-filament::section>
            <x-slot name="heading">📊 Barang Paling Sering Diambil</x-slot>
            @livewire(\App\Filament\Admin\Widgets\TopAtkChart::class)
        </x-filament::section>

    </div>
</x-filament-panels::page>
