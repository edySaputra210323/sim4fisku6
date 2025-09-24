<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SemesterEnum: string implements HasLabel
{
    case GANJIL = 'Ganjil';
    case GENAP = 'Genap';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GANJIL => 'Semester Ganjil',
            self::GENAP => 'Semester Genap',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::GANJIL => 'blue',
            self::GENAP => 'green',
        };
    }

    /**
     * Helper untuk dipakai di Select Filament
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
