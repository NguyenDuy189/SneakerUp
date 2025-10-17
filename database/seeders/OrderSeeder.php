<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create('vi_VN');

        $users = DB::table('users')->pluck('id');
        $providers = DB::table('delivery_providers')->pluck('id');
        $variants = DB::table('product_variants')->pluck('id');

        // Nếu thiếu dữ liệu cơ bản thì bỏ qua
        if ($users->isEmpty() || $providers->isEmpty() || $variants->isEmpty()) {
            echo "⚠️ Thiếu dữ liệu (users, providers hoặc variants). Hãy seed chúng trước!\n";
            return;
        }

        foreach (range(1, 20) as $i) {
            $userId = $users->random();
            $providerId = $providers->random();
            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'code' => 'ORD' . strtoupper(Str::random(8)),
                'fullname' => $faker->name,
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'payment_method' => $faker->randomElement(['COD', 'VNPay', 'BankTransfer', 'Momo']),
                'status' => $faker->randomElement(['pending', 'confirmed', 'shipping', 'completed', 'cancelled']),
                'total_price' => 0,
                'shipping_fee' => $faker->numberBetween(15000, 40000),
                'provider_id' => $providerId,
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
            ]);

            $total = 0;

            // Thêm chi tiết sản phẩm trong đơn
            foreach (range(1, rand(1, 4)) as $j) {
                $variantId = $variants->random();
                $variant = DB::table('product_variants')->where('id', $variantId)->first();
                $product = DB::table('products')->where('id', $variant->product_id)->first();
                $color = DB::table('colors')->where('id', $variant->color_id)->value('color_name');
                $size = DB::table('sizes')->where('id', $variant->size_id)->value('size_value');

                $quantity = rand(1, 3);
                $subtotal = $variant->price * $quantity;

                DB::table('order_details')->insert([
                    'order_id' => $orderId,
                    'variant_id' => $variantId,
                    'product_name' => $product->name,
                    'color_name' => $color,
                    'size_value' => $size,
                    'quantity' => $quantity,
                    'price' => $variant->price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            // Cập nhật tổng tiền đơn hàng
            DB::table('orders')->where('id', $orderId)->update(['total_price' => $total]);
        }
    }
}
