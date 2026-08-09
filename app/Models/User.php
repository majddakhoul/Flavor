<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\UserType;
use App\Support\Traits\Filterable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Filterable;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'gender',
        'status',
        'user_type',
        'location_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'user_type' => UserType::class,
        'gender' => Gender::class,
    ];

    protected array $searchable = ['first_name', 'last_name', 'email', 'phone'];

    protected array $filterable = ['user_type', 'status', 'gender', 'location_id'];

    protected array $sortable = ['first_name', 'last_name', 'email', 'created_at'];

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst($value),
            set: fn (string $value) => ucfirst(trim($value)),
        );
    }

    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value === null ? null : preg_replace('/\s+/', '', $value),
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->first_name . ' ' . $this->last_name),
        )->shouldCache();
    }

    protected function initials(): Attribute
    {
        return Attribute::make(
            get: fn () => mb_strtoupper(mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1)),
        )->shouldCache();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function scopeStaff($query)
    {
        return $query->whereIn('user_type', [UserType::Manager->value, UserType::Employee->value]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function isManager(): bool
    {
        return $this->user_type === UserType::Manager
            || $this->employee?->position === \App\Enums\EmployeePosition::Manager;
    }

    public function isCustomer(): bool
    {
        return $this->user_type === UserType::Customer;
    }

    public function isStaff(): bool
    {
        return $this->user_type?->isStaff() ?? false;
    }

    public function hasAbility(string $ability): bool
    {
        if ($this->isManager()) {
            return true;
        }

        return in_array($ability, $this->employee?->position?->abilities() ?? [], true);
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }
}
