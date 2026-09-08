<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ProtectsBaseline;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code',
    'type',
    'value',
    'currency',
    'limit_uses',
    'use_count',
    'valid_from',
    'valid_until',
    'is_active',
])]
class Coupon extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'limit_uses' => 'integer',
            'use_count' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Domain Logic: Validates if the coupon can be applied right now.
     */
    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->limit_uses !== null && $this->use_count >= $this->limit_uses) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }
}
