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
        Schema::create('post_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên danh mục (ví dụ: Tin tức)
            $table->string('slug')->unique(); // Đường dẫn (ví dụ: tin-tuc)
            $table->boolean('status')->default(true); // Trạng thái (true=hiện, false=ẩn)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_categories');
    }
};
