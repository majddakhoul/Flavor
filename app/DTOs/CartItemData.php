<?php

namespace App\DTOs;

use App\Enums\CartItemType;

final class CartItemData
{
    public function __construct(
        public readonly CartItemType $type,
        public readonly int $id,
        public readonly int $quantity,
        public readonly ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            type: CartItemType::from((string) ($data['type'] ?? 'meal')),
            id: (int) ($data['id'] ?? 0),
            quantity: max(1, (int) ($data['quantity'] ?? 1)),
            notes: isset($data['notes']) && $data['notes'] !== '' ? (string) $data['notes'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'id' => $this->id,
            'quantity' => $this->quantity,
            'notes' => $this->notes,
        ];
    }
}
