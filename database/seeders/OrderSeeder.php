<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        $users = DB::table('users')->where('role', 'customer')->pluck('id');
        $staffs = DB::table('users')->whereIn('role', ['admin', 'staff'])->pluck('id');
        $providers = DB::table('delivery_providers')->pluck('id');
        $variants = DB::table('product_variants')->pluck('id');

        if ($users->isEmpty() || $providers->isEmpty() || $variants->isEmpty()) {
            echo "⚠️ Thiếu dữ liệu users/providers/variants. Hãy seed chúng trước!\n";
            return;
        }

        foreach (range(1, 20) as $i) {
            $userId = $users->random();
            $staffId = $staffs->random();
            $providerId = $providers->random();

            $placedName = $faker->name();
            $placedPhone = $faker->phoneNumber();
            $placedAddress = $faker->address();
            $placedEmail = $faker->safeEmail();

            $receiverName = $faker->name();
            $receiverPhone = $faker->phoneNumber();
            $receiverAddress = $faker->address();
            $receiverEmail = $faker->safeEmail();

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'staff_id' => $staffId,
                'confirm_by' => $staffId,
                'code' => 'ORD' . strtoupper(Str::random(8)),
                'placed_name' => $placedName,
                'placed_phone' => $placedPhone,
                'placed_address' => $placedAddress,
                'placed_email' => $placedEmail,
                'receiver_name' => $receiverName,
                'receiver_phone' => $receiverPhone,
                'receiver_address' => $receiverAddress,
                'receiver_email' => $receiverEmail,
                'payment_method' => $faker->randomElement(['COD', 'VNPay', 'BankTransfer', 'Momo']),
                'status' => $faker->randomElement(['pending', 'confirmed', 'shipping', 'completed', 'cancelled', 'returned']),
                'total_price' => 0,
                'shipping_fee' => $faker->numberBetween(15000, 40000),
                'provider_id' => $providerId,
                'note' => $faker->optional()->sentence(),
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => now(),
            ]);

            $total = 0;

            // Chi tiết sản phẩm
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

            DB::table('orders')->where('id', $orderId)->update(['total_price' => $total]);
        }

        echo "Đã tạo 20 đơn hàng thành công!\n";
    }
}
