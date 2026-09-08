<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ProtectsBaseline;
use App\Observers\FaqObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([FaqObserver::class])]
#[Fillable([
    'question',
    'answer',
    'target_audience',
    'is_active',
    'sort_order',
])]
class Faq extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
