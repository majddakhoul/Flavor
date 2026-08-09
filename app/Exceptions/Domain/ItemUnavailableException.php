<?php

namespace App\Exceptions\Domain;

class ItemUnavailableException extends DomainException
{
    public static function meal(string $name): self
    {
        return new self(__('errors.meal_unavailable', ['name' => $name]), 422);
    }

    public static function offer(string $title): self
    {
        return new self(__('errors.offer_unavailable', ['title' => $title]), 422);
    }
}
