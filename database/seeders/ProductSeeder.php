<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $faker = Faker::create('vi_VN');
        $brands = DB::table('brands')->pluck('id');
        $categories = DB::table('categories')->pluck('id');

        foreach (range(1, 10) as $i) {
            DB::table('products')->insert([
                'name' => 'Giày Sneaker ' . $i,
                'brand_id' => $brands->random(),
                'category_id' => $categories->random(),
                'base_price' => $faker->numberBetween(700000, 2000000),
                'description' => $faker->sentence(10),
                'status' => 'active',
                'created_at' => now(),
            ]);
        }
    }
}
