<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('threads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();

            // Email Infrastructure
            $table->string('mailbox_message_id')->nullable()->unique();
            $table->string('in_reply_to')->nullable(); // Points to the client's original Message-ID
            $table->unsignedTinyInteger('direction'); // mapped to MessageDirection Enum

            // Content
            $table->longText('body_text')->nullable(); // Clean text for AI parsing
            $table->longText('body_html')->nullable(); // Rich text for Filament UI

            // AI & Draft State
            $table->boolean('is_draft')->default(false);
            $table->boolean('is_ai_generated')->default(false);

            // Cloud Assets
            $table->json('attachments')->nullable(); // array of {name: "file.pdf", url: "https..."}

            $table->timestamps();

            // Index for instantly loading a thread's chat history
            $table->index(['thread_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
