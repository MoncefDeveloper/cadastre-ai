<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();

            // Enums
            $table->unsignedTinyInteger('listing_type');
            $table->unsignedTinyInteger('property_type');
            $table->unsignedTinyInteger('status')->default(1);

            // Core Data
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description'); // Stored as Markdown
            $table->string('city')->index(); // Independent index for direct city lookups
            $table->string('address')->nullable();

            // Financials (Stored in cents)
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_price')->nullable();

            // Metrics
            $table->unsignedInteger('area_sqm');
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);

            // Metadata
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            // Composite Index: Optimized for AI query matching
            $table->index(['status', 'listing_type', 'city', 'price'], 'ai_match_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
