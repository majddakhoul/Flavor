<?php

namespace App\Models;

use App\Support\Traits\Filterable;
use App\Support\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use Filterable;
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'unit_cost',
        'is_active',
    ];

    protected $casts = [
        'stock_quantity' => 'integer',
        'unit_cost' => 'integer',
        'is_active' => 'boolean',
    ];

    protected array $translatable = ['name', 'unit'];

    protected array $searchable = ['name', 'unit'];

    protected array $filterable = ['is_active'];

    protected array $sortable = ['name', 'stock_quantity', 'unit_cost', 'created_at'];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function unit(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtolower($value),
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    protected function stockValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock_quantity * $this->unit_cost,
        )->shouldCache();
    }

    protected function isLow(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock_quantity <= config('flavor.inventory.low_stock_threshold'),
        )->shouldCache();
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'ingredient_meal')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '<=', config('flavor.inventory.low_stock_threshold'));
    }

    public function filterLowStock(Builder $query, $value): void
    {
        $value ? $query->lowStock() : $query->where('stock_quantity', '>', config('flavor.inventory.low_stock_threshold'));
    }
}
