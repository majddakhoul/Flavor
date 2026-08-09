<?php

namespace App\DTOs;

final class TableData
{
    public function __construct(
        public readonly string $table_number,
        public readonly int $capacity,
        public readonly string $location,
        public readonly bool $is_active,
        public readonly int $price_per_hour,
        public readonly ?string $description,
        public readonly array $translations,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            table_number: (string) ($data['table_number'] ?? ''),
            capacity: (int) ($data['capacity'] ?? 0),
            location: (string) ($data['location'] ?? ''),
            is_active: (bool) ($data['is_active'] ?? false),
            price_per_hour: (int) ($data['price_per_hour'] ?? 0),
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            translations: (array) ($data['translations'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'table_number' => $this->table_number,
            'capacity' => $this->capacity,
            'location' => $this->location,
            'is_active' => $this->is_active,
            'price_per_hour' => $this->price_per_hour,
            'description' => $this->description,
        ];
    }
}
