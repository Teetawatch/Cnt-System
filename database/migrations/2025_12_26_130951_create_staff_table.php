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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // ชื่อ-นามสกุล
            $table->string('position');                      // ตำแหน่ง (ผอ., รอง ผอ.)
            $table->string('department')->nullable();        // หน่วยงาน/แผนก
            $table->string('photo')->nullable();             // รูปภาพ
            $table->boolean('is_active')->default(true);     // สถานะใช้งาน
            $table->integer('sort_order')->default(0);       // ลำดับการแสดง
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
