<?php

namespace App\Enums;

enum MealAvailability: string
{
    case Available = 'available';
    case Unavailable = 'unavailable';

    public function label(): string
    {
        return __('enums.meal_availability.' . $this->value);
    }

    public function tone(): string
    {
        return $this === self::Available ? 'success' : 'muted';
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
