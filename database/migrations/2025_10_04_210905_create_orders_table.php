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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Người đặt hàng
            $table->unsignedBigInteger('user_id')->nullable()->comment('Người đặt hàng');
            $table->string('code')->unique();

            // Thông tin người đặt
            $table->string('placed_name')->nullable();
            $table->string('placed_phone')->nullable();
            $table->string('placed_address')->nullable();
            $table->string('placed_email')->nullable();

            // Thông tin người nhận
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('receiver_email')->nullable();

            // Phương thức thanh toán và trạng thái
            $table->string('payment_method')->nullable();
            $table->enum('status', [
                'pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'failed', 'returned'
            ])->default('pending');

            // Giá trị đơn hàng
            $table->decimal('total_price', 12, 2)->default(0);
            $table->decimal('shipping_fee', 12, 2)->default(0);

            // Đơn vị vận chuyển (nếu có)
            $table->unsignedBigInteger('provider_id')->nullable();

            // Nhân viên phụ trách
            $table->unsignedBigInteger('staff_id')->nullable()->comment('Nhân viên xử lý đơn');
            $table->string('confirm_by')->nullable()->comment('Nhân viên xác nhận đơn');  // Giữ là string, không FK

            // Voucher (thêm mới)
            $table->unsignedBigInteger('voucher_id')->nullable()->comment('Voucher áp dụng cho đơn hàng');

            // Ghi chú
            $table->text('note')->nullable()->comment('Ghi chú của khách hàng');
            $table->text('internal_note')->nullable()->comment('Ghi chú nội bộ');

            $table->timestamps();

            /**
             * Ràng buộc khóa ngoại
             */
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('provider_id')->references('id')->on('delivery_providers')->onDelete('set null');
            $table->foreign('staff_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');  // FK cho voucher
            // XÓA: $table->foreign('confirm_by')->references('id')->on('users')->onDelete('set null');  // Không cần FK vì confirm_by là string
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};