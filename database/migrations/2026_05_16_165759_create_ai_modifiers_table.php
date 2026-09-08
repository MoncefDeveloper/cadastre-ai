<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_modifiers', function (Blueprint $table) {
            $table->id();
            // Null means Global. If an agent creates their own shortcut, it gets their ID.
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->string('label'); // "Make it Shorter"
            $table->text('instruction')->nullable(); // "Rewrite this strictly under 50 words."

            $table->string('color')->default('primary');

            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_modifiers');
    }
};
