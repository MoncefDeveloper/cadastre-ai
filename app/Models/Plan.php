<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ProtectsBaseline;
use App\Observers\PlanObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([PlanObserver::class])]
#[Fillable([
    'name',
    'slug',
    'description',
    'price_monthly',
    'price_yearly',
    'currency',
    'features',
    'limits',
    'is_active',
    'is_featured',
    'sort_order',
    'mock_subscriber_count',
])]
class Plan extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'price_monthly' => 'integer',
            'price_yearly' => 'integer',
            'features' => 'array',
            'limits' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'mock_subscriber_count' => 'integer',
        ];
    }
}
