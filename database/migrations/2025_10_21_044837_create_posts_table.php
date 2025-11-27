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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // 1. Khóa ngoại liên kết với bảng 'post_categories'
            // constrained(): Tự động liên kết với 'id' trên bảng 'post_categories'
            // onDelete('cascade'): Nếu xóa danh mục, TẤT CẢ bài viết trong đó cũng bị xóa
            $table->foreignId('post_category_id')->constrained('post_categories')->onDelete('cascade');

            $table->string('title'); // Tiêu đề bài viết
            $table->string('slug')->unique(); // Đường dẫn (ví dụ: bai-viet-moi)
            $table->longText('content'); // Nội dung bài viết
            $table->string('image')->nullable(); // Đường dẫn lưu ảnh bìa

            // Trạng thái (draft: nháp, published: đã đăng)
            $table->string('status')->default('draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
