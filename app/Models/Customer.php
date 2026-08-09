<?php

namespace App\Models;

use App\Enums\Allergy;
use App\Enums\ReservationStatus;
use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Customer extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'allergies',
        'favorite_categories',
        'ban',
        'ban_date',
        'user_id',
    ];

    protected $casts = [
        'ban' => 'boolean',
        'ban_date' => 'date',
        'allergies' => Allergy::class,
    ];

    protected array $searchable = ['user.first_name', 'user.last_name', 'user.email', 'user.phone'];

    protected array $filterable = ['ban'];

    protected array $sortable = ['created_at', 'ban_date'];

    protected function banUntil(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->ban_date instanceof Carbon
                ? $this->ban_date->copy()->addDays(config('flavor.reservations.ban_days'))
                : null,
        );
    }

    protected function isBanned(): Attribute
    {
        return Attribute::make(
            get: fn () => (bool) $this->ban && ($this->ban_until === null || $this->ban_until->isFuture()),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function mealRatings(): HasMany
    {
        return $this->hasMany(MealRating::class);
    }

    public function offerRatings(): HasMany
    {
        return $this->hasMany(OfferRating::class);
    }

    public function scopeBanned($query)
    {
        return $query->where('ban', true);
    }

    public function cancelledReservationsCount(): int
    {
        return $this->reservations()->where('status', ReservationStatus::Cancelled->value)->count();
    }
}
