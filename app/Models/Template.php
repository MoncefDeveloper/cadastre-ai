<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Thread\ThreadChannel;
use App\Models\Concerns\ProtectsBaseline;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category_id', 'name', 'channel', 'system_instructions', 'prompt', 'variables', 'rules', 'is_active'])]
class Template extends Model
{
    use HasFactory, ProtectsBaseline;

    protected function casts(): array
    {
        return [
            'channel' => ThreadChannel::class,
            'variables' => 'array', // Automatically handles JSON
            'rules' => 'array', // Automatically handles JSON
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
