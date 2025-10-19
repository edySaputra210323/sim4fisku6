<?php

namespace App\Enums;

enum JamKeEnum: int
{
    case JAM_1 = 1;
    case JAM_2 = 2;
    case JAM_3 = 3;
    case JAM_4 = 4;
    case JAM_5 = 5;
    case JAM_6 = 6;
    case JAM_7 = 7;
    case JAM_8 = 8;
    case JAM_9 = 9;
    case JAM_10 = 10;
    case JAM_11 = 11;
    case JAM_12 = 12;
    case JAM_13 = 13;
    case JAM_14 = 14;

    public function labels(): string
    {
        return "Jam ke-{$this->value}";
    }

   public static function options(): array
   {
    return collect(self::cases())->mapWithKeys(fn($case)=>[$case->value=>$case->labels()])->toArray();
   }
}
