<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();

            $table->string('question');
            $table->text('answer'); // Supports Rich Text / HTML

            $table->string('target_audience', 50)->default('global'); // e.g., global, agents, clients, billing

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Composite Index: Highly optimized for frontend landing pages and portals
            $table->index(['is_active', 'target_audience', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
