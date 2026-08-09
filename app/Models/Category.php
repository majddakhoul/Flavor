<?php

namespace App\Models;

use App\Support\Traits\Filterable;
use App\Support\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use Filterable;
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'name',
        'description',
        'parent_id',
    ];

    protected array $translatable = ['name', 'description'];

    protected array $searchable = ['name', 'description'];

    protected array $filterable = ['parent_id'];

    protected array $sortable = ['name', 'created_at'];

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

    protected function isRoot(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->parent_id === null,
        );
    }

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
