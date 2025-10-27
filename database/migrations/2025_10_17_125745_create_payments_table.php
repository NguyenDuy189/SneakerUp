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
        Schema::create('payments', function (Blueprint $table) {
            //
            $table->id();

            // Liên kết đơn hàng
            $table->unsignedBigInteger('order_id')->nullable();

            // Nhân viên xác nhận thanh toán
            $table->unsignedBigInteger('confirmed_by')->nullable();

            // Thông tin thanh toán
            $table->string('method')->nullable(); // COD, banking, momo, vnpay,...
            $table->enum('status', [
                'pending', 'paid', 'failed', 'refunded', 'cancelled', 'chargeback'
            ])->default('pending');
            $table->string('refund_reference')->nullable()->comment('Mã tham chiếu hoàn tiền nếu có');
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('VND');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('confirmed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
