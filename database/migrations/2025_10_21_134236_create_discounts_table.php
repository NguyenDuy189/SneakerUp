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
        Schema::create('discounts', function (Blueprint $table) {
        // $table->id();
        // $table->string('name');
        // $table->integer('percent')->nullable();
        // $table->decimal('percentage', 5, 2)->nullable(); // phần trăm giảm
        // $table->decimal('amount', 10, 2)->nullable(); // số tiền giảm
        // $table->date('start_date')->nullable();
        // $table->date('end_date')->nullable();
        // $table->timestamps();
    $table->id();
    $table->string('code')->unique(); // mã giảm giá
    $table->string('type')->default('percent'); // loại: percent / fixed
    $table->decimal('value', 8, 2); // giá trị giảm
    $table->integer('usage_limit')->default(0); // giới hạn lượt dùng
    $table->integer('used_count')->default(0); // đã dùng bao nhiêu lần
    $table->date('expiry_date')->nullable(); // ngày hết hạn
    $table->decimal('min_order_value', 10, 2)->nullable(); // giá trị đơn tối thiểu
    $table->boolean('status')->default(true); // đang hoạt động hay không
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
