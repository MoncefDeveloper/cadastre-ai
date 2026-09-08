<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Standard enum string tracking for Postmark state
            $table->string('delivery_status')->nullable()->after('mailbox_message_id');
            $table->timestamp('bounced_at')->nullable()->after('delivery_status');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'bounced_at']);
        });
    }
};
