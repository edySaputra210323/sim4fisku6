<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusSuratMasukEnum: string implements HasLabel
{
    case DITERIMA = 'diterima';
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DITERIMA => 'Diterima',
            self::DIPROSES => 'Diproses',
            self::SELESAI => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DITERIMA => 'blue',
            self::DIPROSES => 'green',
            self::SELESAI => 'red',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
