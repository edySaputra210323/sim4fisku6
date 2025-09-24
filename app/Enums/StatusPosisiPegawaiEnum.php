<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusPosisiPegawaiEnum: string implements HasLabel
{
    case PTY = 'Pegawai Tetap Yayasan';
    case KONTRAK = 'Kontrak';
    case HONORER = 'Honorer';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PTY => 'Pegawai Tetap Yayasan',
            self::KONTRAK => 'Kontrak',
            self::HONORER => 'Honorer',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PTY => 'blue',
            self::KONTRAK => 'green',
            self::HONORER => 'red',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
