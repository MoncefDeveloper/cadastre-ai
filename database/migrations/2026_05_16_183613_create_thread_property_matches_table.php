<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thread_property_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('threads')->cascadeOnDelete();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();

            $table->decimal('match_score', 5, 2); // e.g., 95.50
            $table->text('reasoning')->nullable(); // AI explanation for the match
            $table->boolean('is_rejected')->default(false); // If the agent clicks [Swap/Remove]

            $table->timestamps();

            // Index to quickly load the top valid properties for the right column
            $table->index(['thread_id', 'is_rejected', 'match_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thread_property_matches');
    }
};
