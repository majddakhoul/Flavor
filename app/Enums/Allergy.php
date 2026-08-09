<?php

namespace App\Enums;

enum Allergy: string
{
    case CowsMilk = 'Cows Milk Allergy';
    case Egg = 'Egg Allergy';
    case Peanut = 'Peanut Allergy';
    case TreeNut = 'Tree Nut Allergy';
    case Fish = 'Fish Allergy';
    case Shellfish = 'Shellfish Allergy';
    case Wheat = 'Wheat Allergy';
    case Soy = 'Soy Allergy';
    case Seed = 'Seed Allergies';
    case RedMeat = 'Red Meat Allergy';
    case Fruit = 'Fruit Allergies';
    case Vegetable = 'Vegetable Allergies';
    case Spice = 'Spice Allergies';

    public function label(): string
    {
        return __('enums.allergy.' . $this->value);
    }

    public static function options(): array
    {
        return array_map(fn (self $case) => ['value' => $case->value, 'label' => $case->label()], self::cases());
    }
}
