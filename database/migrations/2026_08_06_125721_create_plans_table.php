<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            // Core Identity
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            // Financials (Stored in cents/halalas)
            $table->unsignedBigInteger('price_monthly')->default(0);
            $table->unsignedBigInteger('price_yearly')->default(0);
            $table->string('currency', 3)->default('SAR');

            // Feature Flags & Limits
            $table->json('features')->nullable(); // Flat array of strings
            $table->json('limits')->nullable();   // Flat key-value object

            // Display Settings & Metrics
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->unsignedInteger('mock_subscriber_count')->default(0);

            $table->timestamps();

            // Composite Index: Optimized for frontend cache loading
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
