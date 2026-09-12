<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->t('title'),
            'description' => $this->t('description'),
            'discount_amount' => $this->discount_amount,
            'is_active' => $this->is_active,
            'is_running' => $this->is_running,
            'is_orderable' => $this->is_orderable,
            'in_stock' => $this->in_stock,
            'price' => $this->price,
            'final_price' => $this->final_price,
            'discount_margin' => $this->discount_margin,
            'rating_average' => $this->rating_average,
            'image_url' => $this->image_url,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'meals' => MealResource::collection($this->whenLoaded('meals')),
        ];
    }
}
