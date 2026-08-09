<?php

namespace App\Exceptions\Domain;

class InsufficientStockException extends DomainException
{
    public static function forIngredient(string $ingredient, string $item, float $required, float $available): self
    {
        return new self(
            __('errors.insufficient_stock', [
                'ingredient' => $ingredient,
                'item' => $item,
                'required' => $required,
                'available' => $available,
            ]),
            422,
            compact('ingredient', 'item', 'required', 'available')
        );
    }
}
