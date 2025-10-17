<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        //
        $faker = Faker::create('vi_VN');
        $products = DB::table('products')->pluck('id');
        $colors = DB::table('colors')->pluck('id');
        $sizes = DB::table('sizes')->pluck('id');

        foreach ($products as $productId) {
            foreach (range(1, 3) as $i) {
                DB::table('product_variants')->insert([
                    'product_id' => $productId,
                    'color_id' => $colors->random(),
                    'size_id' => $sizes->random(),
                    'price' => $faker->numberBetween(700000, 2000000),
                    'stock' => rand(5, 20),
                    'sku' => 'SKU-' . strtoupper(Str::random(6)),
                    'created_at' => now(),
                ]);
            }
        }
    }
}
