<?php

namespace App\DTOs;

final class EmployeeData
{
    public function __construct(
        public readonly string $national_id,
        public readonly string $position,
        public readonly int $salary,
        public readonly ?int $bonus,
        public readonly ?string $notes,
        public readonly string $hire_date,
        public readonly string $birth_date,
        public readonly string $first_name,
        public readonly string $last_name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $gender,
        public readonly ?int $location_id,
        public readonly ?string $password,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            national_id: (string) ($data['national_id'] ?? ''),
            position: (string) ($data['position'] ?? ''),
            salary: (int) ($data['salary'] ?? 0),
            bonus: isset($data['bonus']) && $data['bonus'] !== '' ? (int) $data['bonus'] : null,
            notes: isset($data['notes']) && $data['notes'] !== '' ? (string) $data['notes'] : null,
            hire_date: (string) ($data['hire_date'] ?? ''),
            birth_date: (string) ($data['birth_date'] ?? ''),
            first_name: (string) ($data['first_name'] ?? ''),
            last_name: (string) ($data['last_name'] ?? ''),
            email: (string) ($data['email'] ?? ''),
            phone: (string) ($data['phone'] ?? ''),
            gender: (string) ($data['gender'] ?? ''),
            location_id: isset($data['location_id']) && $data['location_id'] !== '' ? (int) $data['location_id'] : null,
            password: isset($data['password']) && $data['password'] !== '' ? (string) $data['password'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'national_id' => $this->national_id,
            'position' => $this->position,
            'salary' => $this->salary,
            'bonus' => $this->bonus,
            'notes' => $this->notes,
            'hire_date' => $this->hire_date,
            'birth_date' => $this->birth_date,
        ];
    }
}
