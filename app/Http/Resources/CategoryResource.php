<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->t('name'),
            'description' => $this->t('description'),
            'parent_id' => $this->parent_id,
            'is_root' => $this->is_root,
            'meals_count' => $this->whenCounted('meals'),
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
