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
        UserSeeder::class,
        BrandSeeder::class,
        CategorySeeder::class,
        ColorSeeder::class,
        SizeSeeder::class,
        ProductSeeder::class,
        ProductVariantSeeder::class,
        DeliveryProviderSeeder::class,
        OrderSeeder::class, // sẽ thêm sau
        PaymentSeeder::class, // sẽ thêm sau
    ]);
    }
}
