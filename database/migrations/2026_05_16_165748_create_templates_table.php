<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('name');
            $table->unsignedTinyInteger('channel')->default(1); // 1 = EMAIL, mapped to ThreadChannel Enum

            // The AI Logic
            $table->text('system_instructions')->nullable(); // e.g., "You are an elite agent. Be persuasive but polite."
            $table->text('prompt'); // e.g., "Greet {{client_name}}, propose {{property_count}} properties..."
            $table->json('variables')->nullable(); // e.g., ["client_name", "property_count"]
            $table->json('rules')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['channel', 'category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
