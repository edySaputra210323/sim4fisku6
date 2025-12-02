<x-filament-widgets::widget>
    <x-filament::section>
        <div class="w-full">

            {{-- HEADER TANGGAL --}}
            <div class="mb-3 pb-2 border-b border-gray-200">
                <h3 class="text-sm font-semibold text-gray-600">
                    📅 Periode: {{ $today }}
                </h3>
            </div>

            {{-- MINI TABLE --}}
            <table class="w-full text-sm mb-3">
                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Total Kelas</td>
                    <td class="py-2 text-right font-semibold text-gray-800">
                        {{ $totalKelas }}
                    </td>
                </tr>

                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Sudah Absensi Hari Ini</td>
                    <td class="py-2 text-right font-semibold text-green-600">
                        {{ $sudahAbsensi }}
                    </td>
                </tr>

                <tr class="border-b border-gray-200">
                    <td class="py-2 font-medium text-gray-700">Belum Absensi Hari Ini</td>
                    <td class="py-2 text-right font-semibold text-red-600">
                        {{ $belumAbsensi }}
                    </td>
                </tr>

                <tr>
                    <td class="py-2 font-medium text-gray-700">Persentase</td>
                    <td class="py-2 text-right font-semibold text-blue-600">
                        {{ $persentase }}%
                    </td>
                </tr>
            </table>

            {{-- DAFTAR KELAS (BADGE RAPI) --}}
            @if ($belumAbsensi > 0)
                <div class="mt-2">
                    <h4 class="text-sm font-semibold mb-1 text-gray-700">
                        Kelas yang belum mengisi absensi:
                    </h4>

                    <div class="flex flex-wrap gap-1">
                        @foreach ($kelasBelumAbsensi as $kelas)
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700" style="color: white; background-color: #f50000; font-weight: bold;">
                                {{ $kelas }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-sm text-green-700 font-semibold">
                    Semua kelas sudah absensi hari ini 🎉
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
