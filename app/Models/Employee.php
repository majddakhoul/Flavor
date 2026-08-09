<?php

namespace App\Models;

use App\Enums\EmployeePosition;
use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use Filterable;
    use HasFactory;

    protected $fillable = [
        'national_id',
        'position',
        'salary',
        'bonus',
        'notes',
        'hire_date',
        'birth_date',
        'user_id',
    ];

    protected $casts = [
        'position' => EmployeePosition::class,
        'salary' => 'integer',
        'bonus' => 'integer',
        'hire_date' => 'date',
        'birth_date' => 'date',
    ];

    protected array $searchable = ['national_id', 'user.first_name', 'user.last_name', 'user.email'];

    protected array $filterable = ['position'];

    protected array $sortable = ['salary', 'bonus', 'hire_date', 'created_at'];

    protected function nationalId(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => preg_replace('/\D/', '', $value),
        );
    }

    protected function notes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value === null ? null : ucfirst($value),
            set: fn (?string $value) => $value === null ? null : trim($value),
        );
    }

    protected function compensation(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) $this->salary + (int) $this->bonus,
        )->shouldCache();
    }

    protected function seniorityYears(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hire_date?->diffInYears(now()) ?? 0,
        )->shouldCache();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
