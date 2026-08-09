<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferRating extends Model
{
    use HasFactory;

    protected $table = 'offers_ratings';

    protected $fillable = [
        'number_stars',
        'offer_id',
        'customer_id',
    ];

    protected $casts = [
        'number_stars' => 'integer',
    ];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
