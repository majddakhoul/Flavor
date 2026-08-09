<?php

namespace App\DTOs;

final class LocationData
{
    public function __construct(
        public readonly string $country,
        public readonly string $region,
        public readonly string $state,
        public readonly string $city,
        public readonly ?string $street,
        public readonly string $delivery_time,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            country: (string) ($data['country'] ?? ''),
            region: (string) ($data['region'] ?? ''),
            state: (string) ($data['state'] ?? ''),
            city: (string) ($data['city'] ?? ''),
            street: isset($data['street']) && $data['street'] !== '' ? (string) $data['street'] : null,
            delivery_time: (string) ($data['delivery_time'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'country' => $this->country,
            'region' => $this->region,
            'state' => $this->state,
            'city' => $this->city,
            'street' => $this->street,
            'delivery_time' => $this->delivery_time,
        ];
    }
}
