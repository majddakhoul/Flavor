<?php

namespace App\Enums;

enum ReservationType: string
{
    case Locally = 'Locally';
    case Application = 'Application';

    public function label(): string
    {
        return __('enums.reservation_type.' . $this->value);
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
