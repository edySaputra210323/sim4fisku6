<?php

namespace App\Enums;

use Filament\Support\HasLabel;

enum TipePrestasiPelanggaranEnum: string implements HasLabel
{
    const PRESTASI = 'Prestasi';
    const PELANGGARAN = 'Pelanggaran';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PRESTASI => 'Prestasi',
            self::PELANGGARAN => 'Pelanggaran',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PRESTASI => 'success',   // kuning
            self::PELANGGARAN => 'danger',  // hijau
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
