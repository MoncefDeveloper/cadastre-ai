<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject')->nullable();

            $table->text('message');
            $table->string('ip_address', 45)->nullable(); // For spam tracking

            $table->string('status', 20)->default('unread'); // unread, read, resolved

            $table->timestamps();

            // Composite Index: Optimized for instantly loading the unread inbox
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
