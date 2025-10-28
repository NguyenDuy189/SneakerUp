<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagsTableSeeder extends Seeder
{
    public function run()
    {
        $tags = ['Hot', 'New', 'Sale'];
        foreach ($tags as $name) {
            Tag::firstOrCreate(['name' => $name]);
        }
    }
}