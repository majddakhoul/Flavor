<?php

namespace App\DTOs;

final class CategoryData
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?int $parent_id,
        public readonly array $translations,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: (string) ($data['name'] ?? ''),
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            parent_id: isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
            translations: (array) ($data['translations'] ?? []),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parent_id,
        ];
    }
}
