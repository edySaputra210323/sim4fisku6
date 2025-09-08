<x-filament-panels::page>
    <x-slot name="heading">
        Invoice Transaksi ATK
    </x-slot>

    <div class="space-y-6">
        {{-- Infolist Transaksi --}}
        <x-filament::section>
            {{ $this->infolist }}
        </x-filament::section>

        {{-- Tombol Aksi --}}
        <x-filament::button.group>
            <x-filament::button 
                tag="a" 
                :href="route('filament.admin.resources.atk-keluars.index')" 
                color="secondary">
                Kembali ke Daftar Transaksi
            </x-filament::button>

            <x-filament::button 
                color="primary" 
                wire:click="downloadInvoice">
                Cetak PDF
            </x-filament::button>
        </x-filament::button.group>
    </div>
</x-filament-panels::page>
