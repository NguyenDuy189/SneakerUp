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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();

            // 1. Cột này liên kết địa chỉ với một người dùng
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // 2. Các cột thông tin y hệt như trong bảng 'orders'
            $table->string('fullname');
            $table->string('phone', 20);
            $table->string('address');

            // 3. Cột này rất quan trọng:
            // Dùng để đánh dấu đâu là địa chỉ "Mặc định"
            $table->boolean('is_default')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
