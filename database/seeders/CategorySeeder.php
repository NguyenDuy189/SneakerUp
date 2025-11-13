<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Danh sách danh mục cha => con
        $categories = [
            'Sneaker' => ['Nike', 'Adidas', 'Jordan', 'Converse'],
            'Running' => ['Nike Running', 'Adidas Ultraboost', 'Asics', 'New Balance'],
            'Casual'  => ['Vans', 'Puma', 'Reebok'],
            'Basketball' => ['Air Jordan', 'Kobe', 'LeBron', 'Curry']
        ];

        // Xoá dữ liệu cũ (nếu muốn reset)
        DB::table('categories')->delete();

        $parentOrder = 1; // thứ tự danh mục cha
        foreach ($categories as $parent => $children) {
            $parentSlug = Str::slug($parent);

            // Insert danh mục cha
            $parentId = DB::table('categories')->insertGetId([
                'name' => $parent,
                'slug' => $parentSlug,
                'parent_id' => null,
                'sort_order' => $parentOrder,
                'position' => $parentOrder,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $childOrder = 1; // thứ tự danh mục con
            foreach ($children as $child) {
                DB::table('categories')->insert([
                    'name' => $child,
                    'slug' => Str::slug($parentSlug . '-' . $child),
                    'parent_id' => $parentId,
                    'sort_order' => $childOrder,
                    'position' => $childOrder,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $childOrder++;
            }

            $parentOrder++;
        }

        $this->command->info('Seeder categories chạy xong! ✅');
    }
}
