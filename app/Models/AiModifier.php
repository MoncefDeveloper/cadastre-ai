<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ProtectsBaseline;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'label', 'instruction', 'color', 'sort_order', 'is_active'])]
class AiModifier extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
