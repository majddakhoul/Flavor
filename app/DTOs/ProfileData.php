<?php

namespace App\DTOs;

final class ProfileData
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $phone,
        public readonly string $gender,
        public readonly ?int $location_id,
        public readonly ?string $allergies,
        public readonly ?string $favorite_categories,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            first_name: (string) ($data['first_name'] ?? ''),
            last_name: (string) ($data['last_name'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            gender: (string) ($data['gender'] ?? ''),
            location_id: isset($data['location_id']) && $data['location_id'] !== '' ? (int) $data['location_id'] : null,
            allergies: isset($data['allergies']) && $data['allergies'] !== '' ? (string) $data['allergies'] : null,
            favorite_categories: isset($data['favorite_categories']) && $data['favorite_categories'] !== '' ? (string) $data['favorite_categories'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'location_id' => $this->location_id,
        ];
    }
}
