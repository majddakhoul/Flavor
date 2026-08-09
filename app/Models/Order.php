<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use Filterable;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'notes',
        'status',
        'order_type',
        'dated_at',
        'customer_id',
        'employee_id',
        'reservation_id',
        'location_id',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'order_type' => OrderType::class,
        'dated_at' => 'date',
    ];

    protected array $searchable = ['notes', 'customer.user.first_name', 'customer.user.last_name'];

    protected array $filterable = ['status', 'order_type', 'customer_id', 'employee_id', 'location_id'];

    protected array $sortable = ['dated_at', 'created_at', 'status'];

    protected function reference(): Attribute
    {
        return Attribute::make(
            get: fn () => 'FLV-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT),
        )->shouldCache();
    }

    protected function notes(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null ? null : trim($value),
        );
    }

    protected function mealsTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('meals.ingredients');

                return (int) $this->meals->sum(fn (Meal $meal) => $meal->price * $meal->pivot->quantity);
            },
        )->shouldCache();
    }

    protected function offersTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('offers.meals.ingredients');

                return (int) $this->offers->sum(fn (Offer $offer) => $offer->final_price * $offer->pivot->quantity);
            },
        )->shouldCache();
    }

    protected function tablesTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->order_type !== OrderType::Reservation) {
                    return 0;
                }

                $this->loadMissing('reservation.tables');

                return (int) ($this->reservation?->tables->sum('price_per_hour') ?? 0);
            },
        )->shouldCache();
    }

    protected function total(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->meals_total + $this->offers_total + $this->tables_total,
        )->shouldCache();
    }

    protected function itemsCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing(['meals', 'offers']);

                return (int) $this->meals->sum('pivot.quantity') + (int) $this->offers->sum('pivot.quantity');
            },
        )->shouldCache();
    }

    protected function estimatedDelivery(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_type === OrderType::Delivery ? $this->location?->estimated_delivery : null,
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function meals(): BelongsToMany
    {
        return $this->belongsToMany(Meal::class, 'meal_order')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_order')
            ->withPivot('quantity', 'notes')
            ->withTimestamps();
    }

    public function scopeStatus(Builder $query, OrderStatus $status): Builder
    {
        return $query->where('status', $status->value);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('dated_at', now()->toDateString());
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [OrderStatus::Pending->value, OrderStatus::Confirmed->value]);
    }

    public function scopeOwnedBy(Builder $query, ?int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    public function filterDateFrom(Builder $query, $value): void
    {
        $query->whereDate('dated_at', '>=', $value);
    }

    public function filterDateTo(Builder $query, $value): void
    {
        $query->whereDate('dated_at', '<=', $value);
    }
}
