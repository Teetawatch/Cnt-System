<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('line_notification_settings', function (Blueprint $table) {
            // Rename token field for Messaging API
            $table->renameColumn('line_notify_token', 'channel_access_token');
            
            // Add destination settings for push messages
            $table->enum('send_mode', ['broadcast', 'push'])->default('broadcast')->after('is_enabled');
            $table->string('destination_id')->nullable()->after('send_mode'); // group ID or user ID for push
            $table->string('destination_name')->nullable()->after('destination_id'); // friendly name for the destination
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('line_notification_settings', function (Blueprint $table) {
            $table->renameColumn('channel_access_token', 'line_notify_token');
            $table->dropColumn(['send_mode', 'destination_id', 'destination_name']);
        });
    }
};
