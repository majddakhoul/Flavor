<?php

namespace App\DTOs;

final class MealData
{
    public function __construct(
        public readonly string $name,
        public readonly string $prep_time,
        public readonly bool $is_vegetarian,
        public readonly int $percentage,
        public readonly ?string $description,
        public readonly string $availability,
        public readonly int $category_id,
        public readonly ?int $picture_id,
        public readonly array $translations,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            prep_time: (string) ($data['prep_time'] ?? ''),
            is_vegetarian: (bool) ($data['is_vegetarian'] ?? false),
            percentage: (int) ($data['percentage'] ?? 0),
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            availability: (string) ($data['availability'] ?? ''),
            category_id: (int) ($data['category_id'] ?? 0),
            picture_id: isset($data['picture_id']) && $data['picture_id'] !== '' ? (int) $data['picture_id'] : null,
            translations: (array) ($data['translations'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'prep_time' => $this->prep_time,
            'is_vegetarian' => $this->is_vegetarian,
            'percentage' => $this->percentage,
            'description' => $this->description,
            'availability' => $this->availability,
            'category_id' => $this->category_id,
            'picture_id' => $this->picture_id,
        ];
    }
}
