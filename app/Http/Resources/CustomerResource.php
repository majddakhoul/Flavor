<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'allergies' => $this->allergies?->value,
            'favorite_categories' => $this->favorite_categories,
            'is_banned' => $this->is_banned,
            'ban_until' => $this->ban_until?->toDateString(),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
