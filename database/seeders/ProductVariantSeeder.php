<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        $products = DB::table('products')->pluck('id');
        $colors = DB::table('colors')->pluck('id');
        $sizes = DB::table('sizes')->pluck('id');

        // Kiểm tra dữ liệu cơ bản có tồn tại không
        if ($products->isEmpty() || $colors->isEmpty() || $sizes->isEmpty()) {
            echo "⚠️ Thiếu dữ liệu (products, colors hoặc sizes). Hãy seed chúng trước!\n";
            return;
        }

        $insertData = [];

        foreach ($products as $productId) {
            // Mỗi sản phẩm tạo ngẫu nhiên 3–5 biến thể
            $variantCount = rand(3, 5);

            for ($i = 0; $i < $variantCount; $i++) {
                $colorId = $colors->random();
                $sizeId = $sizes->random();

                // Kiểm tra trùng biến thể cùng màu + size
                $exists = DB::table('product_variants')
                    ->where('product_id', $productId)
                    ->where('color_id', $colorId)
                    ->where('size_id', $sizeId)
                    ->exists();

                if ($exists) continue; // Bỏ qua nếu đã có biến thể này

                $insertData[] = [
                    'product_id' => $productId,
                    'color_id' => $colorId,
                    'size_id' => $sizeId,
                    'price' => $faker->numberBetween(700000, 2500000),
                    'stock' => rand(5, 30),
                    'sku' => 'SKU-' . strtoupper(Str::random(6)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Chèn 1 lần để tối ưu hiệu năng
        if (!empty($insertData)) {
            DB::table('product_variants')->insert($insertData);
            echo "Đã thêm " . count($insertData) . " biến thể sản phẩm thành công!\n";
        } else {
            echo "Không có biến thể mới nào được thêm (có thể đã đủ dữ liệu).\n";
        }
    }
}
