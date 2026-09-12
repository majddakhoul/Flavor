<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MealResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->t('name'),
            'description' => $this->t('description'),
            'prep_time' => $this->prep_time,
            'is_vegetarian' => $this->is_vegetarian,
            'availability' => $this->availability->value,
            'availability_label' => $this->availability->label(),
            'prep_cost' => $this->prep_cost,
            'profit_margin' => $this->profit_margin,
            'percentage' => $this->percentage,
            'price' => $this->price,
            'in_stock' => $this->in_stock,
            'max_portions' => $this->max_portions,
            'is_orderable' => $this->is_orderable,
            'rating_average' => $this->rating_average,
            'image_url' => $this->image_url,
            'picture' => new PictureResource($this->whenLoaded('picture')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')),
        ];
    }
}
