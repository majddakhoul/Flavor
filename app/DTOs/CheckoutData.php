<?php

namespace App\DTOs;

final class CheckoutData
{
    public function __construct(
        public readonly ?string $notes,
        public readonly ?int $location_id,
        public readonly ?int $reservation_id,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            notes: isset($data['notes']) && $data['notes'] !== '' ? (string) $data['notes'] : null,
            location_id: isset($data['location_id']) && $data['location_id'] !== '' ? (int) $data['location_id'] : null,
            reservation_id: isset($data['reservation_id']) && $data['reservation_id'] !== '' ? (int) $data['reservation_id'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'notes' => $this->notes,
            'location_id' => $this->location_id,
            'reservation_id' => $this->reservation_id,
        ];
    }
}
