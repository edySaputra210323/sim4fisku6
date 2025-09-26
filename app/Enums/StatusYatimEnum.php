<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StatusYatimEnum: string implements HasLabel
{
    case YATIM = 'Yatim';
    case PIATU = 'Piatu';
    case YATIM_PIATU = 'Yatim Piatu';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::YATIM => 'Yatim',
            self::PIATU => 'Piatu',
            self::YATIM_PIATU => 'Yatim Piatu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::YATIM => 'info',
            self::PIATU => 'info',
            self::YATIM_PIATU => 'warning',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
