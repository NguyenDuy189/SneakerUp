<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (['Đen', 'Trắng', 'Xanh', 'Đỏ'] as $color) {
            DB::table('colors')->insert(['color_name' => $color]);
        }
    }
}
