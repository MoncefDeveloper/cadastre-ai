<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ProtectsBaseline;
use App\Observers\PropertyImageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([PropertyImageObserver::class])]
#[Fillable(['property_id', 'image_path', 'sort_order'])]
class PropertyImage extends Model
{
    use HasFactory, ProtectsBaseline;

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
