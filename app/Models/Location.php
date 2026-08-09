<?php

namespace App\Models;

use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Location extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'country',
        'region',
        'delivery_time',
        'state',
        'city',
        'street',
    ];

    protected array $searchable = ['country', 'region', 'state', 'city', 'street'];

    protected array $filterable = ['country', 'state', 'city'];

    protected array $sortable = ['city', 'state', 'country', 'created_at'];

    protected function city(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function region(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn () => implode(' / ', array_filter([$this->city, $this->region, $this->state])),
        )->shouldCache();
    }

    protected function estimatedDelivery(): Attribute
    {
        return Attribute::make(
            get: fn () => Carbon::parse($this->delivery_time)
                ->addMinutes(config('flavor.orders.delivery_buffer_minutes'))
                ->format('H:i'),
        )->shouldCache();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
