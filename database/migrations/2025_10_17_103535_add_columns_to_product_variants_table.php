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
        Schema::table('product_variants', function (Blueprint $table) {
            // $table->string('size')->nullable();
            // $table->string('color')->nullable();
            // $table->integer('stock')->default(0);
            // $table->decimal('price', 15, 2)->nullable();
            // $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['size', 'color', 'stock', 'price', 'image']);
        });
    }
};
