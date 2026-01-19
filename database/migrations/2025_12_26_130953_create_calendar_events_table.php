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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');  // ผู้ปฏิบัติ
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // ผู้บันทึก
            $table->date('event_date');                         // วันที่
            $table->time('start_time');                         // เวลาเริ่ม
            $table->time('end_time')->nullable();               // เวลาสิ้นสุด
            $table->string('title');                            // รายการงาน/หัวข้อ
            $table->text('description')->nullable();            // รายละเอียด
            $table->string('location');                         // สถานที่
            $table->string('organization')->nullable();         // หน่วยงานที่เชิญ/จัด
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->timestamps();

            // Indexes for faster queries
            $table->index('event_date');
            $table->index(['staff_id', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
