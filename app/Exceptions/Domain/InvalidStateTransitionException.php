<?php

namespace App\Exceptions\Domain;

class InvalidStateTransitionException extends DomainException
{
    public static function between(string $from, string $to): self
    {
        return new self(__('errors.invalid_transition', ['from' => $from, 'to' => $to]), 422);
    }

    public static function locked(string $state): self
    {
        return new self(__('errors.state_locked', ['state' => $state]), 422);
    }
}
