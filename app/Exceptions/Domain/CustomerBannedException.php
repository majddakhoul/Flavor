<?php

namespace App\Exceptions\Domain;

use Illuminate\Support\Carbon;

class CustomerBannedException extends DomainException
{
    public static function until(?Carbon $until): self
    {
        return new self(
            __('errors.customer_banned', ['date' => $until?->translatedFormat('d M Y') ?? '-']),
            403
        );
    }

    public static function threshold(int $cancelled): self
    {
        return new self(__('errors.customer_banned_threshold', ['count' => $cancelled]), 403);
    }
}
