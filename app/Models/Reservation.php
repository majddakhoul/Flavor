<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Enums\ReservationType;
use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Reservation extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'reservation_code',
        'party_size',
        'status',
        'type',
        'date',
        'special_requests',
        'customer_id',
        'employee_id',
        'order_id',
    ];

    protected $casts = [
        'status' => ReservationStatus::class,
        'type' => ReservationType::class,
        'party_size' => 'integer',
        'date' => 'date',
    ];

    protected array $searchable = ['reservation_code', 'special_requests'];

    protected array $filterable = ['status', 'type', 'customer_id', 'employee_id'];

    protected array $sortable = ['date', 'party_size', 'created_at'];

    protected function reservationCode(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }

    protected function specialRequests(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : ucfirst($value),
            set: fn (?string $value) => $value === null ? null : trim($value),
        );
    }

    protected function startsAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('tables');
                $value = $this->tables->min(fn (Table $table) => $table->pivot->start_time);

                return $value ? Carbon::parse($value) : null;
            },
        )->shouldCache();
    }

    protected function endsAt(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('tables');
                $value = $this->tables->max(fn (Table $table) => $table->pivot->end_time);

                return $value ? Carbon::parse($value) : null;
            },
        )->shouldCache();
    }

    protected function durationHours(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->starts_at && $this->ends_at
                ? max(1, $this->starts_at->diffInHours($this->ends_at))
                : 0,
        )->shouldCache();
    }

    protected function tablesCost(): Attribute
    {
        return Attribute::make(
            get: function () {
                $this->loadMissing('tables');

                return (int) $this->tables->sum('price_per_hour') * $this->duration_hours;
            },
        )->shouldCache();
    }

    protected function seatsBooked(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->tables->sum('capacity'),
        )->shouldCache();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function tables(): BelongsToMany
    {
        return $this->belongsToMany(Table::class)
            ->withPivot('start_time', 'end_time')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [ReservationStatus::Pending->value, ReservationStatus::Confirmed->value]);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereDate('date', '>=', now()->toDateString());
    }

    public function scopeOwnedBy(Builder $query, ?int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    public function filterDate(Builder $query, $value): void
    {
        $query->whereDate('date', $value);
    }

    public function filterDateFrom(Builder $query, $value): void
    {
        $query->whereDate('date', '>=', $value);
    }

    public function filterDateTo(Builder $query, $value): void
    {
        $query->whereDate('date', '<=', $value);
    }
}
