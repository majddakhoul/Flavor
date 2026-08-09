<?php

namespace App\DTOs;

final class OfferData
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly int $discount_amount,
        public readonly bool $is_active,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly array $translations,
        public readonly array $meals,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: (string) ($data['title'] ?? ''),
            description: (string) ($data['description'] ?? ''),
            discount_amount: (int) ($data['discount_amount'] ?? 0),
            is_active: (bool) ($data['is_active'] ?? false),
            start_date: (string) ($data['start_date'] ?? ''),
            end_date: (string) ($data['end_date'] ?? ''),
            translations: (array) ($data['translations'] ?? []),
            meals: (array) ($data['meals'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'discount_amount' => $this->discount_amount,
            'is_active' => $this->is_active,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }
}
