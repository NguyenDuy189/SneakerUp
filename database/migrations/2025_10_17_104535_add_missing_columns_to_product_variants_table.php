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
            if (!Schema::hasColumn('product_variants', 'size')) {
                $table->string('size')->nullable();
            }
            if (!Schema::hasColumn('product_variants', 'color')) {
                $table->string('color')->nullable();
            }
            if (!Schema::hasColumn('product_variants', 'stock')) {
                $table->integer('stock')->default(0);
            }
            if (!Schema::hasColumn('product_variants', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('product_variants', 'image')) {
                $table->string('image')->nullable();
            }
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
