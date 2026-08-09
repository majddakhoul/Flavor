<?php

namespace App\Enums;

enum OrderType: string
{
    case Delivery = 'Delivery';
    case Reservation = 'Reservation';
    case Takeaway = 'Takeaway';

    public function label(): string
    {
        return __('enums.order_type.' . $this->value);
    }

    public function requiresLocation(): bool
    {
        return $this === self::Delivery;
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
