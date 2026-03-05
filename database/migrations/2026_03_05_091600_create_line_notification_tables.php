<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Settings table - singleton pattern (only 1 row)
        Schema::create('line_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('line_notify_token')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->boolean('schedule_enabled')->default(false);
            $table->time('schedule_time')->default('07:00');
            $table->text('message_template')->nullable();
            $table->timestamps();
        });

        // Logs table
        Schema::create('line_notification_logs', function (Blueprint $table) {
            $table->id();
            $table->date('notification_date'); // วันที่ของข้อมูลที่ส่ง
            $table->enum('send_type', ['manual', 'scheduled'])->default('manual');
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('message_sent')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('events_count')->default(0);
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_notification_logs');
        Schema::dropIfExists('line_notification_settings');
    }
};
