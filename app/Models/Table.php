<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use App\Enums\TableLocation;
use App\Support\Traits\Filterable;
use App\Support\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Table extends Model
{
    use Filterable;
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'table_number',
        'capacity',
        'location',
        'is_active',
        'price_per_hour',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
        'price_per_hour' => 'integer',
        'location' => TableLocation::class,
    ];

    protected array $translatable = ['description'];

    protected array $searchable = ['table_number', 'description'];

    protected array $filterable = ['location', 'is_active', 'capacity'];

    protected array $sortable = ['table_number', 'capacity', 'price_per_hour'];

    protected function tableNumber(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => strtoupper($value),
            set: fn (string $value) => strtoupper(trim($value)),
        );
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: fn () => __('domain.table_label', ['number' => $this->table_number]),
        );
    }

    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class)
            ->withPivot('start_time', 'end_time')
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFitting(Builder $query, int $partySize): Builder
    {
        return $query->where('capacity', '>=', $partySize);
    }

    public function scopeFreeBetween(Builder $query, string $start, string $end): Builder
    {
        return $query->whereDoesntHave('reservations', function (Builder $reservation) use ($start, $end) {
            $reservation->where('reservations.status', '!=', ReservationStatus::Cancelled->value)
                ->where('reservation_table.start_time', '<', $end)
                ->where('reservation_table.end_time', '>', $start);
        });
    }
}
