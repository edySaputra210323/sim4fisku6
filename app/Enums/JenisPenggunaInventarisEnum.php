<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JenisPenggunaInventarisEnum: string implements HasLabel
{
    case TETAP = 'tetap';
    case PERMANEN = 'permanen';
    case MOBILE = 'mobile';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::TETAP => 'Tetap',
            self::PERMANEN => 'Permanen',
            self::MOBILE => 'Mobile',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TETAP => 'warning',   // kuning
            self::MOBILE => 'success',  // hijau
            self::PERMANEN => 'danger', // merah
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }

    // helper robust: terima enum instance atau string (case-insensitive)
    public static function resolve(mixed $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        foreach (self::cases() as $case) {
            if (strcasecmp($case->value, (string) $value) === 0) {
                return $case;
            }
        }

        return null;
    }
}
