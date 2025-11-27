<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // Thêm cột để lưu nội dung phản hồi của admin
            $table->text('admin_reply')->nullable()->after('status');

            // Thêm cột để lưu ngày admin phản hồi
            $table->timestamp('replied_at')->nullable()->after('admin_reply');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            // (Để có thể rollback)
            $table->dropColumn('admin_reply');
            $table->dropColumn('replied_at');
        });
    }
};
