<?php

namespace App\Enums;

enum TableLocation: string
{
    case Indoor = 'Indoor';
    case Outdoor = 'Outdoor';
    case VIP = 'VIP';
    case Roof = 'Roof';

    public function label(): string
    {
        return __('enums.table_location.' . $this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::Indoor => 'indoor',
            self::Outdoor => 'outdoor',
            self::VIP => 'vip',
            self::Roof => 'roof',
        };
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
