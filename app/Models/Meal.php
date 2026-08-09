<?php

namespace App\Models;

use App\Enums\MealAvailability;
use App\Support\Traits\Filterable;
use App\Support\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meal extends Model
{
    use Filterable;
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'prep_time',
        'is_vegetarian',
        'percentage',
        'description',
        'availability',
        'picture_id',
        'category_id',
    ];

    protected $casts = [
        'is_vegetarian' => 'boolean',
        'percentage' => 'integer',
        'availability' => MealAvailability::class,
    ];

    protected array $translatable = ['name', 'description'];

    protected array $searchable = ['name', 'description', 'category.name'];

    protected array $filterable = ['category_id', 'availability', 'is_vegetarian'];

    protected array $sortable = ['name', 'percentage', 'created_at'];

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null ? null : trim($value),
        );
    }

    protected function prepCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('ingredients');

                return (int) $this->ingredients->sum(
                    fn (Ingredient $ingredient) => $ingredient->unit_cost * $ingredient->pivot->quantity
                );
            },
        )->shouldCache();
    }

    protected function profitMargin(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) ceil(($this->percentage / 100) * $this->prep_cost),
        )->shouldCache();
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->prep_cost + $this->profit_margin,
        )->shouldCache();
    }

    protected function inStock(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('ingredients');

                if ($this->ingredients->isEmpty()) {
                    return false;
                }

                return $this->ingredients->every(
                    fn (Ingredient $ingredient) => $ingredient->stock_quantity >= $ingredient->pivot->quantity
                );
            },
        )->shouldCache();
    }

    protected function maxPortions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('ingredients');

                if ($this->ingredients->isEmpty()) {
                    return 0;
                }

                return (int) $this->ingredients->min(
                    fn (Ingredient $ingredient) => $ingredient->pivot->quantity > 0
                        ? floor($ingredient->stock_quantity / $ingredient->pivot->quantity)
                        : 0
                );
            },
        )->shouldCache();
    }

    protected function isOrderable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->availability === MealAvailability::Available && $this->in_stock,
        );
    }

    protected function ratingAverage(): Attribute
    {
        return Attribute::make(
            get: fn () => round((float) $this->ratings()->avg('number_stars'), 1),
        )->shouldCache();
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->picture?->url ?? asset('assets/img/meals/placeholder.svg'),
        )->shouldCache();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function picture(): BelongsTo
    {
        return $this->belongsTo(Picture::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_meal')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'meal_offer')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'meal_order')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(MealRating::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('availability', MealAvailability::Available->value);
    }

    public function scopeVegetarian(Builder $query): Builder
    {
        return $query->where('is_vegetarian', true);
    }

    public function filterVegetarian(Builder $query, $value): void
    {
        $query->where('is_vegetarian', (bool) $value);
    }

    public function filterCategory(Builder $query, $value): void
    {
        $query->whereIn('category_id', (array) $value);
    }
}
