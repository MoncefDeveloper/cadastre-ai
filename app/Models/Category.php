<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Category\CategoryType;
use App\Models\Concerns\ProtectsBaseline;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'color', 'is_active', 'type', 'parent_id'])]
class Category extends Model
{
    use HasFactory, ProtectsBaseline;
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'type' => CategoryType::class,
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
