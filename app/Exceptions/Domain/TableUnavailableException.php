<?php

namespace App\Exceptions\Domain;

class TableUnavailableException extends DomainException
{
    public static function forTables(array $tableNumbers): self
    {
        return new self(
            __('errors.tables_unavailable', ['tables' => implode(', ', $tableNumbers)]),
            409,
            ['tables' => $tableNumbers]
        );
    }

    public static function capacity(int $capacity, int $partySize): self
    {
        return new self(
            __('errors.capacity_exceeded', ['capacity' => $capacity, 'party' => $partySize]),
            422
        );
    }
}
