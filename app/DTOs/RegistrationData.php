<?php

namespace App\DTOs;

final class RegistrationData
{
    public function __construct(
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $gender,
        public readonly ?int $location_id,
        public readonly string $password,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            first_name: (string) ($data['first_name'] ?? ''),
            last_name: (string) ($data['last_name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            gender: (string) ($data['gender'] ?? ''),
            location_id: isset($data['location_id']) && $data['location_id'] !== '' ? (int) $data['location_id'] : null,
            password: (string) ($data['password'] ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'location_id' => $this->location_id,
        ];
    }
}
