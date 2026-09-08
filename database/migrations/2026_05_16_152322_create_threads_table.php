<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();

            // Core Relationships
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            // External Routing (Mailgun / WhatsApp)
            $table->string('mailbox_hash')->nullable()->unique();

            // Thread Metadata
            $table->string('subject')->nullable();
            $table->unsignedTinyInteger('status')->default(1); // NEW
            $table->unsignedTinyInteger('priority')->default(1); // NORMAL
            $table->unsignedTinyInteger('channel')->default(1); // EMAIL
            $table->boolean('is_unread')->default(true);
            $table->timestamp('snoozed_until')->nullable();

            // The AI Brain Memory
            $table->json('extracted_criteria')->nullable();

            // Denormalized caching for instant Inbox sorting
            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            // INDEXES (Crucial for Inbox performance)
            // 1. Used to instantly load an agent's active inbox, newest first
            $table->index(['assigned_user_id', 'status', 'last_message_at']);
            // 2. Used to quickly find snoozed threads that need to wake up
            $table->index(['status', 'snoozed_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
