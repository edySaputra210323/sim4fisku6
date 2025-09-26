<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TipeMutasiEnum: string implements HasLabel
{
    case MASUK = 'Masuk';
    case KELUAR = 'Keluar';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MASUK => 'Masuk',
            self::KELUAR => 'Keluar',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MASUK => 'success',   // kuning
            self::KELUAR => 'danger',  // hijau
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
