<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (['Sneaker', 'Running', 'Casual', 'Basketball'] as $category) {
            DB::table('categories')->insert([
                'name' => $category,
                'created_at' => now(),
            ]);
        }
    }
}
