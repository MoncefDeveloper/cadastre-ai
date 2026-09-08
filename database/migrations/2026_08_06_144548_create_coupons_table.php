<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            // Core Identity
            $table->string('code')->unique();
            $table->string('type', 20)->default('percentage'); // 'percentage' or 'fixed'

            // Value stored dynamically: 1-100 for percentage, or cents for fixed amount
            $table->unsignedBigInteger('value');
            $table->string('currency', 3)->nullable(); // Only used if type is 'fixed'

            // Usage Limits
            $table->unsignedInteger('limit_uses')->nullable();
            $table->unsignedInteger('use_count')->default(0);

            // Time Boundaries
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_until')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Composite Index: Optimized for real-time checkout validation
            $table->index(['is_active', 'valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
