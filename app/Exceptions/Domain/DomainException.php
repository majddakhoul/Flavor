<?php

namespace App\Exceptions\Domain;

use RuntimeException;
use Throwable;

class DomainException extends RuntimeException
{
    public function __construct(
        string $message = '',
        protected int $status = 422,
        protected array $context = [],
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $status, $previous);
    }

    public function status(): int
    {
        return $this->status;
    }

    public function context(): array
    {
        return $this->context;
    }

    public function userMessage(): string
    {
        return $this->getMessage();
    }
}
