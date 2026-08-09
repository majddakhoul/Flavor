<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Picture extends Model
{
    use HasFactory;

    protected $fillable = ['path', 'name'];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => str_starts_with($this->path, 'assets/')
                ? asset($this->path)
                : Storage::disk(config('flavor.media.disk'))->url($this->path),
        )->shouldCache();
    }

    public function meal(): HasOne
    {
        return $this->hasOne(Meal::class);
    }
}
