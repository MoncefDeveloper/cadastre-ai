<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // The event type (mapped to our Enum)
            $table->string('notification_type');

            // Channels
            $table->boolean('channel_database')->default(true);
            $table->boolean('channel_mail')->default(true);
            $table->boolean('channel_sms')->default(false); // Future Mock
            $table->boolean('channel_whatsapp')->default(false); // Future Mock

            $table->timestamps();

            // Composite Unique Index (Rule: One preference row per event type, per user)
            $table->unique(['user_id', 'notification_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
