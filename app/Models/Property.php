<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Property\ListingType;
use App\Enums\Property\PropertyStatus;
use App\Enums\Property\PropertyType;
use App\Models\Concerns\ProtectsBaseline;
use App\Observers\PropertyObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


#[ObservedBy([PropertyObserver::class])]
#[Fillable([
    'agent_id',
    'category_id',
    'listing_type',
    'property_type',
    'status',
    'title',
    'slug',
    'description',
    'city',
    'address',
    'price',
    'discount_price',
    'area_sqm',
    'bedrooms',
    'bathrooms',
    'is_featured'
])]
class Property extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'listing_type' => ListingType::class,
            'property_type' => PropertyType::class,
            'status' => PropertyStatus::class,
            'is_featured' => 'boolean',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }
}
