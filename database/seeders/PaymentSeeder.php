<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // Lấy tất cả đơn hàng
        $orders = DB::table('orders')->pluck('id');

        if ($orders->isEmpty()) {
            echo "⚠️ Chưa có đơn hàng nào. Hãy chạy OrderSeeder trước!\n";
            return;
        }

        foreach ($orders as $orderId) {
            // Lấy ngẫu nhiên 1 staff xác nhận (có role admin hoặc staff)
            $staffId = DB::table('users')
                ->whereIn('role', ['admin', 'staff'])
                ->inRandomOrder()
                ->value('id');

            DB::table('payments')->insert([
                'order_id'      => $orderId,
                'confirmed_by'  => $staffId,
                'method'        => $faker->randomElement(['COD', 'VNPay', 'BankTransfer', 'Momo']),
                'status'        => $faker->randomElement([
                    'pending', 'paid', 'failed', 'refunded', 'cancelled', 'chargeback'
                ]),
                'amount'        => $faker->numberBetween(50000, 5000000),
                'currency'      => 'VND',
                'paid_at'       => $faker->dateTimeBetween('-2 months', 'now'),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        echo "✅ Đã tạo thanh toán cho " . count($orders) . " đơn hàng.\n";
    }
}
