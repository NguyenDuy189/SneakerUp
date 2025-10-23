<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserSeeder::class,              // tạo user trước
            BrandSeeder::class,             // cần cho Product
            CategorySeeder::class,          // cần cho Product
            ColorSeeder::class,             // cần cho ProductVariant
            SizeSeeder::class,              // cần cho ProductVariant
            ProductSeeder::class,           // tạo Product trước ProductVariant
            ProductVariantSeeder::class,    // tạo variants trước Order
            DeliveryProviderSeeder::class,  // cần cho Order
            OrderSeeder::class,             // tạo đơn hàng
            PaymentSeeder::class,           // tạo thanh toán sau khi có đơn
        ]);
    }
}
