<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('discounts')->insert([
            [
                'name' => 'Giảm 10%',
                'percent' => 10,
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(10),
            ],
            [
                'name' => 'Giảm 20%',
                'percent' => 20,
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(5),
            ],
        ]);
    }
}