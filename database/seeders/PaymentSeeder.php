<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create('vi_VN');
        $orders = DB::table('orders')->pluck('id');

        if ($orders->isEmpty()) {
            echo "⚠️ Chưa có đơn hàng nào. Hãy chạy OrderSeeder trước!\n";
            return;
        }

        foreach ($orders as $orderId) {
            $order = DB::table('orders')->where('id', $orderId)->first();
            DB::table('payments')->insert([
                'order_id' => $orderId,
                'method' => $faker->randomElement(['COD', 'VNPay', 'BankTransfer', 'Momo']),
                'status' => $faker->randomElement(['pending', 'paid', 'failed']),
                'transaction_code' => strtoupper('PAY' . substr(md5($orderId . now()), 0, 8)),
                'amount' => $order->total_price,
                'paid_at' => $order->status === 'completed' ? now() : null,
                'created_at' => now(),
            ]);
        }
    }
}
