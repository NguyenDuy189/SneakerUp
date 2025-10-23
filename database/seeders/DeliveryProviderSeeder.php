<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        foreach (['Giao Hàng Nhanh', 'Giao Hàng Tiết Kiệm', 'Viettel Post', 'J&T Express', 'Ninja Vam'] as $provider) {
        DB::table('delivery_providers')->updateOrInsert(
            ['name' => $provider],
            ['status' => 'active',
            'created_at' => now()]
        );
    }

    }
}
