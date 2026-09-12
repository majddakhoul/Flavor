<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->t('name'),
            'unit' => $this->t('unit'),
            'stock_quantity' => $this->stock_quantity,
            'unit_cost' => $this->unit_cost,
            'stock_value' => $this->stock_value,
            'is_active' => $this->is_active,
            'is_low' => $this->is_low,
            'quantity_in_recipe' => $this->when(
                $this->pivot !== null,
                fn () => $this->pivot?->quantity
            ),
        ];
    }
}
