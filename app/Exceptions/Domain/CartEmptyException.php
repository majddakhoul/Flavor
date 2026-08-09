<?php

namespace App\Exceptions\Domain;

class CartEmptyException extends DomainException
{
    public static function make(): self
    {
        return new self(__('errors.cart_empty'), 422);
    }
}
