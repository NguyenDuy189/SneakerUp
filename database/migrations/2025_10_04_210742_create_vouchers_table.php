<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Mã voucher
            $table->string('name'); // Tên voucher
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage'); // Loại giảm giá
            $table->decimal('discount_value', 10, 2); // Giá trị giảm
            $table->decimal('min_order_value', 10, 2)->nullable(); // Giá trị đơn tối thiểu
            $table->decimal('max_discount', 10, 2)->nullable(); // Giảm tối đa (cho percentage)
            $table->integer('usage_limit')->nullable(); // Giới hạn sử dụng
            $table->integer('used_count')->default(0); // Số lần đã dùng
            $table->timestamp('expiry_date')->nullable(); // Ngày hết hạn
            $table->boolean('is_active')->default(true); // Kích hoạt
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};