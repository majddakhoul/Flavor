<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'national_id' => $this->national_id,
            'position' => $this->position->value,
            'position_label' => $this->position->label(),
            'abilities' => $this->position->abilities(),
            'salary' => $this->salary,
            'bonus' => $this->bonus,
            'compensation' => $this->compensation,
            'notes' => $this->notes,
            'hire_date' => $this->hire_date?->toDateString(),
            'seniority_years' => $this->seniority_years,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
