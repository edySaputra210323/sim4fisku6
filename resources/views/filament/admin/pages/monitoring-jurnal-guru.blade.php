<div class="flex flex-wrap items-end gap-4 p-2 bg-gray-50 rounded-xl shadow-sm border border-gray-200">
    {{-- 📅 Filter tanggal --}}
    <div class="flex flex-col">
        <label class="text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <x-filament::input.wrapper>
            <x-filament::input type="date" wire:model.live="tanggal" />
        </x-filament::input.wrapper>
    </div>

    {{-- 👩‍🏫 Filter guru --}}
    <div class="flex flex-col">
        <label class="text-sm font-medium text-gray-700 mb-1">Guru</label>
        <x-filament::input.wrapper>
            <select
                wire:model.live="filterGuru"
                class="fi-input block w-48 rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            >
                <option value="">Semua Guru</option>
                @foreach ($this->guruList as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </x-filament::input.wrapper>
    </div>

    {{-- 🏫 Filter kelas --}}
    <div class="flex flex-col">
        <label class="text-sm font-medium text-gray-700 mb-1">Kelas</label>
        <x-filament::input.wrapper>
            <select
                wire:model.live="filterKelas"
                class="fi-input block w-40 rounded-lg border-gray-300 text-sm focus:border-primary-500 focus:ring-primary-500"
            >
                <option value="">Semua Kelas</option>
                @foreach ($this->kelasList as $id => $nama)
                    <option value="{{ $id }}">{{ $nama }}</option>
                @endforeach
            </select>
        </x-filament::input.wrapper>
    </div>

    {{-- 🔍 Tombol tampilkan --}}
    <div class="flex items-end">
        <x-filament::button wire:click="loadData" color="primary" icon="heroicon-o-magnifying-glass">
            Tampilkan
        </x-filament::button>
    </div>
</div>
