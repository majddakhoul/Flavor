<?php

namespace App\Models;

use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'maintenance_item',
        'total_price',
        'price',
        'discount',
        'notes',
        'employee_id',
    ];

    protected $casts = [
        'price' => 'integer',
        'total_price' => 'integer',
        'discount' => 'integer',
    ];

    protected array $searchable = ['maintenance_item', 'notes'];

    protected array $filterable = ['employee_id'];

    protected array $sortable = ['price', 'total_price', 'created_at'];

    protected function maintenanceItem(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function savedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->price - (int) $this->total_price,
        )->shouldCache();
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
