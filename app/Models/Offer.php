<?php

namespace App\Models;

use App\Support\Traits\Filterable;
use App\Support\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offer extends Model
{
    use Filterable;
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'discount_amount',
        'is_active',
        'start_date',
        'end_date',
        'picture_id',
    ];

    protected $casts = [
        'discount_amount' => 'integer',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected array $translatable = ['title', 'description'];

    protected array $searchable = ['title', 'description'];

    protected array $filterable = ['is_active'];

    protected array $sortable = ['title', 'discount_amount', 'start_date', 'end_date'];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucwords($value),
            set: fn (string $value) => ucwords(trim($value)),
        );
    }

    protected function price(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('meals.ingredients');

                return (int) $this->meals->sum(fn (Meal $meal) => $meal->price * $meal->pivot->quantity);
            },
        )->shouldCache();
    }

    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) round($this->price - ($this->price * $this->discount_amount / 100)),
        )->shouldCache();
    }

    protected function discountMargin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->price - $this->final_price,
        )->shouldCache();
    }

    protected function isRunning(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_active
                && $this->start_date?->startOfDay()->lte(now())
                && $this->end_date?->endOfDay()->gte(now()),
        )->shouldCache();
    }

    protected function inStock(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('meals.ingredients');

                return $this->meals->isNotEmpty() && $this->meals->every(fn (Meal $meal) => $meal->in_stock);
            },
        )->shouldCache();
    }

    protected function isOrderable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->is_running && $this->in_stock,
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
            get: fn () => $this->picture?->url ?? asset('assets/img/offers/placeholder.svg'),
        )->shouldCache();
    }

    public function picture(): BelongsTo
    {
        return $this->belongsTo(Picture::class);
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_offer')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'offer_order')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(OfferRating::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeRunning(Builder $query): Builder
    {
        return $query->active()
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }

    public function filterRunning(Builder $query, $value): void
    {
        $value ? $query->running() : $query->where('is_active', false);
    }
}
