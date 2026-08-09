<?php

namespace App\Enums;

enum CartItemType: string
{
    case Meal = 'meal';
    case Offer = 'offer';

    public function label(): string
    {
        return __('enums.cart_item_type.' . $this->value);
    }

    public function bucket(): string
    {
        return $this === self::Meal ? 'meals' : 'offers';
    }
}
